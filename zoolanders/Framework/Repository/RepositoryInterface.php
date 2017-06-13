<?php

namespace Zoolanders\Framework\Repository;

interface RepositoryInterface
{
    public function create(array $data);

    public function all();

    public function get($id);

    public function delete($ids);
}
