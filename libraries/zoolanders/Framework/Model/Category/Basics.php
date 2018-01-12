<?php

namespace Zoolanders\Framework\Model\Category;

trait Basics
{
    /**
     * Apply general filters like searchable, published, etc
     */
    protected function filterIds ($ids)
    {
        $this->setState('id', $ids);
        return $this->filterIn('id', $ids);
    }

    protected function filterName ($name)
    {
        $this->setState('name', $name);
        return $this->where('name', 'like', $name);
    }

    protected function filterApp ($applications)
    {
        $this->setState('app', $applications);
        return $this->filterIn('application_id', $applications);
    }

    protected function filterApplication ($applications)
    {
        return $this->filterApp($applications);
    }

    protected function filterApplicationId ($applications)
    {
       return $this->filterApplication($applications);
    }

    protected function filterPublished ($state = 1)
    {
        $this->setState('published', $state);
        $state = (int)$state;
        $this->wherePrefix('published = ' . $state);
        return $this;
    }

    protected function filterCreators ($ids)
    {
        return $this->filterIn('created_by', $ids);
    }

    protected function filterEditors ($ids)
    {
        return $this->filterIn('modified_by', $ids);
    }

    /**
     * Created - related date search
     */
    protected function filterCreated ($value)
    {
        $this->filterDateTime($this->getQuery()->qn('a.created'), $value);
    }

    protected function filterCreatedTo ($value)
    {
        $this->filterDateTimeTo($this->getQuery()->qn('a.created'), $value);
    }

    protected function filterCreatedFrom ($value)
    {
        $this->filterDateTimeFrom($this->getQuery()->qn('a.created'), $value);
    }

    protected function filterCreatedBetween ($from, $to)
    {
        $this->filterDateTimeBetween($this->getQuery()->qn('a.created'), $from, $to);
    }

    protected function filterCreatedWithinInterval ($interval, $unit)
    {
        $this->filterDateWithinInterval($this->getQuery()->qn('a.created'), $interval, $unit);
    }

    /**
     * Modified - related date search
     */
    protected function filterModified ($value)
    {
        $this->filterDateTime($this->getQuery()->qn('a.modified'), $value);
    }

    protected function filterModifiedTo ($value)
    {
        $this->filterDateTimeTo($this->getQuery()->qn('a.modified'), $value);
    }

    protected function filterModifiedFrom ($value)
    {
        $this->filterDateTimeFrom($this->getQuery()->qn('a.modified'), $value);
    }

    protected function filterModifiedBetween ($from, $to)
    {
        $this->filterDateTimeBetween($this->getQuery()->qn('a.modified'), $from, $to);
    }

    protected function filterModifiedWithinInterval ($interval, $unit)
    {
        $this->filterDateWithinInterval($this->getQuery()->qn('a.modified'), $interval, $unit);
    }
}
