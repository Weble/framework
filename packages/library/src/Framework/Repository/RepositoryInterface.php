<?php

namespace Zoolanders\Framework\Repository;

interface RepositoryInterface
{
    public function create (array $data);

    public function all ();

    public function find ($id);

    public function delete ($ids);
}
