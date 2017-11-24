<?php

namespace AppBundle\Repository;

use AppBundle\Entity\Title;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

/**
 * Title Repository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TitleRepository extends EntityRepository
{

    /**
     * Return the next title by ID.
     *
     * @param Title $title
     * @return Title|Null
     */
    public function next(Title $title) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.id > :id');
        $qb->setParameter('id', $title->getId());
        $qb->addOrderBy('e.id', 'ASC');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Return the next title by ID.
     *
     * @param Title $title
     * @return Title|Null
     */
    public function previous(Title $title) {
        $qb = $this->createQueryBuilder('e');
        $qb->andWhere('e.id < :id');
        $qb->setParameter('id', $title->getId());
        $qb->addOrderBy('e.id', 'DESC');
        $qb->setMaxResults(1);
        return $qb->getQuery()->getOneOrNullResult();
    }

    /**
     * Simple MySQL fulltext search via MATCH AGAINST.
     *
     * @param string $q
     * @return Query
     */
    public function search($q) {
        $qb = $this->createQueryBuilder('e');
        $qb->addSelect("MATCH (e.title) AGAINST (:q BOOLEAN) as score");
        $qb->add('where', "MATCH (e.title) AGAINST (:q BOOLEAN) > 0.5");
        $qb->orderBy('score', 'desc');
        $qb->setParameter('q', $q);
        return $qb->getQuery();
    }

    /**
     * Build a complex search query from form data.
     *
     * @param array $data
     * @return Query
     */
    public function buildSearchQuery($data = array()) {
        $qb = $this->createQueryBuilder('e');
        if (isset($data['title']) && $data['title']) {
            $qb->andWhere("MATCH (e.title) AGAINST (:title BOOLEAN) > 0");
            $qb->setParameter('title', $data['title']);
        }
        if(isset($data['checked'])) {
            $qb->andWhere('e.checked = :checked');
            $qb->setParameter('checked', $data['checked'] == 'Y');
        }
        if(isset($data['finalcheck'])) {
            $qb->andWhere('e.finalcheck = :finalcheck');
            $qb->setParameter('finalcheck', $data['finalcheck'] == 'Y');
        }
        if (isset($data['pubdate']) && $data['pubdate']) {
            $m = array();
            if (preg_match('/^\s*[0-9]{4}\s*$/', $data['pubdate'])) {
                $qb->andWhere("YEAR(STRTODATE(e.pubdate, '%Y')) = :year");
                $qb->setParameter('year', $data['pubdate']);
            } elseif (preg_match('/^\s*(\*|[0-9]{4})\s*-\s*(\*|[0-9]{4})\s*$/', $data['pubdate'], $m)) {
                $from = ($m[1] === '*' ? -1 : $m[1]);
                $to = ($m[2] === '*' ? 9999 : $m[2]);
                $qb->andWhere(":from <= YEAR(STRTODATE(e.pubdate, '%Y')) AND YEAR(STRTODATE(e.pubdate, '%Y')) <= :to");
                $qb->setParameter('from', $from);
                $qb->setParameter('to', $to);
            }
        }
        if (isset($data['location']) && $data['location']) {
            $qb->innerJoin('e.locationOfPrinting', 'g');
            $qb->andWhere("MATCH(g.alternatenames, g.name) AGAINST (:location BOOLEAN) > 0");
            $qb->setParameter('location', $data['location']);
        }
        if (isset($data['format']) && is_array($data['format']) && count($data['format'])) {
            $qb->andWhere('e.format IN (:formats)');
            $qb->setParameter('formats', $data['format']);
        }
        if (isset($data['genre']) && is_array($data['genre']) && count($data['genre'])) {
            $qb->andWhere('e.genre IN (:genres)');
            $qb->setParameter('genres', $data['genre']);
        }
        if (isset($data['signed_author']) && $data['signed_author']) {
            $qb->andWhere("MATCH (e.signedAuthor) AGAINST(:signedAuthor BOOLEAN) > 0");
            $qb->setParameter('signedAuthor', $data['signed_author']);
        }
        if (isset($data['imprint']) && $data['imprint']) {
            $qb->andWhere("MATCH (e.imprint) AGAINST(:imprint BOOLEAN) > 0");
            $qb->setParameter('imprint', $data['imprint']);
        }
        if (isset($data['pseudonym']) && $data['pseudonym']) {
            $qb->andWhere("MATCH (e.pseudonym) AGAINST (:pseudonym BOOLEAN) > 0");
            $qb->setParameter('pseudonym', $data['pseudonym']);
        }
        if (isset($data['orderby']) && $data['orderby']) {
            $dir = 'ASC';
            if (preg_match('/^(?:asc|desc)$/i', $data['orderdir'])) {
                $dir = $data['orderdir'];
            }
            switch ($data['orderby']) {
                case 'pubdate':
                    $qb->orderBy('e.pubdate', $dir);
                    break;
                case 'title':
                default:
                    $qb->orderBy('e.title', $dir);
                    break;
            }
        }

        // only add the title filter query parts if the subform has data.
        if (isset($data['person_filter']) && count(array_filter($data['person_filter']))) {
            $filter = $data['person_filter'];
            $idx = '00';
            $trAlias = 'tr_' . $idx;
            $pAlias = 'p_' . $idx;
            $qb->innerJoin('e.titleRoles', $trAlias)->innerJoin("{$trAlias}.person", $pAlias);
            if (isset($filter['name']) && $filter['name']) {
                $qb->andWhere("MATCH ({$pAlias}.lastName, {$pAlias}.firstName, {$pAlias}.title) AGAINST(:{$pAlias}_name BOOLEAN') > 0");
                $qb->setParameter("{$pAlias}_name", $filter['name']);
            }
            if (isset($filter['gender']) && $filter['gender']) {
                $genders = [];
                if (in_array('M', $filter['gender'])) {
                    $genders[] = 'M';
                }
                if (in_array('F', $filter['gender'])) {
                    $genders[] = 'F';
                }
                if (in_array('U', $filter['gender'])) {
                    $genders[] = '';
                }
                $qb->andWhere("{$pAlias}.gender in (:genders)");
                $qb->setParameter('genders', $genders);
            }

            if(isset($filter['person_role']) && count($filter['person_role']) > 0) {
                $qb->andWhere("{$trAlias}.role in (:roles_{$idx})");
                $qb->setParameter("roles_{$idx}", $filter['person_role']);
            }
        }

        // only add the firm filter query parts if the subform has data.
        if(isset($data['firm_filter']) && count(array_filter($data['firm_filter']))) {
            $filter = $data['firm_filter'];
            $idx = '01';
            $tfrAlias = 'tfr_' . $idx;
            $fAlias = 'f_' . $idx;
            $qb->innerJoin('e.titleFirmroles', $tfrAlias)->innerJoin("{$tfrAlias}.firm", $fAlias);
            if(isset($filter['firm_name']) && $filter['firm_name']) {
                $qb->andWhere("MATCH({$fAlias}.name) AGAINST(:{$fAlias}_name BOOLEAN) > 0");
                $qb->setParameter("{$fAlias}_name", $filter['firm_name']);
            }
            if(isset($filter['firm_role']) && count($filter['firm_role']) > 0) {
                $qb->andWhere("{$tfrAlias}.firmrole in (:firmroles_{$idx})");
                $qb->setParameter("firmroles_{$idx}", $filter['firm_role']);
            }
            if(isset($filter['firm_address']) && $filter['firm_address']) {
                $qb->andWhere("MATCH({$fAlias}.streetAddress) AGAINST(:{$fAlias}_address BOOLEAN) > 0");
                $qb->setParameter("{$fAlias}_address", $filter['firm_address']);
            }
        }

        return $qb->getQuery();
    }

    /**
     * Find and return $limit random titles.
     *
     * @param int $limit
     * @return Collection
     */
    public function random($limit) {
        $qb = $this->createQueryBuilder('e');
        $qb->orderBy('RAND()');
        $qb->setMaxResults($limit);
        return $qb->getQuery()->execute();
    }
}
