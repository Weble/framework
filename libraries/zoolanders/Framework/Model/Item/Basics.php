<?php

namespace Zoolanders\Framework\Model\Item;

use Zoolanders\Framework\Model\Database\Access;

trait Basics {
    use Access;

    /**
     * Apply general filters like searchable, published, etc
     */
    protected function filterIds ($ids) {
        return $this->filterIn('id', $ids);
    }

    protected function filterName ($name) {
        return $this->where('name', 'like', $name);
    }

    protected function filterApplication ($applications) {
        return $this->filterIn('application_id', $applications);
    }

    protected function filterType ($types) {
        return $this->filterIn('type', $types);
    }

    protected function filterSearchable ($state = 1) {
        $state = (int)$state;
        $this->wherePrefix('searchable = ' . $state);
        return $this;
    }

    protected function filterState ($state = 1) {
        $state = (int)$state;
        $this->wherePrefix('state = ' . $state);
        return $this;
    }

    protected function filterCreators ($ids) {
        return $this->filterIn('created_by', $ids);
    }

    protected function filterEditors ($ids) {
        return $this->filterIn('modified_by', $ids);
    }

    protected function filterFrontpage () {
        $this->join(ZOO_TABLE_CATEGORY_ITEM, "{$this->getQuery()->qn($this->tablePrefix)}.id = f.item_id", "f");
        $this->whereRaw('f.category_id = 0');
    }

    /**
     * Created - related date search
     */
    protected function filterCreated ($value) {
        $this->filterDateTime($this->getQuery()->qn('a.created'), $value);
    }

    protected function filterCreatedTo ($value) {
        $this->filterDateTimeTo($this->getQuery()->qn('a.created'), $value);
    }

    protected function filterCreatedFrom ($value) {
        $this->filterDateTimeFrom($this->getQuery()->qn('a.created'), $value);
    }

    protected function filterCreatedBetween ($from, $to) {
        $this->filterDateTimeBetween($this->getQuery()->qn('a.created'), $from, $to);
    }

    protected function filterCreatedWithinInterval ($interval, $unit) {
        $this->filterDateWithinInterval($this->getQuery()->qn('a.created'), $interval, $unit);
    }

    /**
     * Modified - related date search
     */
    protected function filterModified ($value) {
        $this->filterDateTime($this->getQuery()->qn('a.modified'), $value);
    }

    protected function filterModifiedTo ($value) {
        $this->filterDateTimeTo($this->getQuery()->qn('a.modified'), $value);
    }

    protected function filterModifiedFrom ($value) {
        $this->filterDateTimeFrom($this->getQuery()->qn('a.modified'), $value);
    }

    protected function filterModifiedBetween ($from, $to) {
        $this->filterDateTimeBetween($this->getQuery()->qn('a.modified'), $from, $to);
    }

    protected function filterModifiedWithinInterval ($interval, $unit) {
        $this->filterDateWithinInterval($this->getQuery()->qn('a.modified'), $interval, $unit);
    }

    /**
     * Publish Up - related date search
     */
    protected function filterPublishedUp ($value) {
        $this->filterDateTime($this->getQuery()->qn('a.publish_up'), $value);
    }

    protected function filterPublishedUpTo ($value) {
        $this->filterDateTimeTo($this->getQuery()->qn('a.publish_up'), $value);
    }

    protected function filterPublishedUpFrom ($value) {
        $this->filterDateTimeFrom($this->getQuery()->qn('a.publish_up'), $value);
    }

    protected function filterPublishedUpBetween ($from, $to) {
        $this->filterDateTimeBetween($this->getQuery()->qn('a.publish_up'), $from, $to);
    }

    protected function filterPublishedUpWithinInterval ($interval, $unit) {
        $this->filterDateWithinInterval($this->getQuery()->qn('a.publish_up'), $interval, $unit);
    }

    /**
     * Publish Down - related date search
     */
    protected function filterPublishedDown ($value) {
        $this->filterDateTime($this->getQuery()->qn('a.publish_down'), $value);
    }

    protected function filterPublishedDownTo ($value) {
        $this->filterDateTimeTo($this->getQuery()->qn('a.publish_down'), $value);
    }

    protected function filterPublishedDownFrom ($value) {
        $this->filterDateTimeFrom($this->getQuery()->qn('a.publish_down'), $value);
    }

    protected function filterPublishedDownBetween ($from, $to) {
        $this->filterDateTimeBetween($this->getQuery()->qn('a.publish_down'), $from, $to);
    }

    protected function filterPublishedDownWithinInterval ($interval, $unit) {
        $this->filterDateWithinInterval($this->getQuery()->qn('a.publish_down'), $interval, $unit);
    }

    /**
     * Get only the published items
     */
    protected function filterPublished () {
        // of course state has to be 1
        $this->state(1);

        $null = $this->container->db->getNullDate();
        $now = new \JDate();

        // within the published dates
        $where = [
            $this->query->qn('a.publish_down') . ' = ' . $this->query->q($null),
            $this->query->qn('a.publish_down') . ' >= ' . $this->query->q($now)
        ];

        $this->query->where('( ' . implode(" OR ", $where) . ')');

        $where = [
            $this->query->qn('a.publish_up') . ' = ' . $this->query->q($null),
            $this->query->qn('a.publish_up') . ' <= ' . $this->query->q($now)
        ];

        $this->query->where('( ' . implode(" OR ", $where) . ')');
    }
}
