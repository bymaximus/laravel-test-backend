<?php

namespace App\Containers\Geral\Data\Repositories;

use App\Ship\Parents\Repositories\Repository;

/**
 * Class AuditModelRepository
 */
class AuditModelRepository extends Repository
{
	public $auditTable = null;

	/**
	 * @var array
	 */
	protected $fieldSearchable = [
		'id' => '=',
	];

	/**
	 * @return Model
	 * @throws RepositoryException
	 */
	public function makeModel()
	{
		parent::makeModel();
		if ($this->auditTable) {
			$this->model->setTable($this->auditTable);
		}
		return $this->model;
	}

	public function setTable($table = null)
	{
		$this->resetModel();
		if ($table) {
			$this->model->setTable($table);
		}
	}

	public function getTable()
	{
		if ($this->model) {
			return $this->model->getTable();
		}
		return null;
	}
}
