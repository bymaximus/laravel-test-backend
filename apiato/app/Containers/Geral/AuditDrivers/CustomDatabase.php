<?php

namespace App\Containers\Geral\AuditDrivers;

use OwenIt\Auditing\Drivers\Database;
use OwenIt\Auditing\Contracts\Auditable;
use OwenIt\Auditing\Contracts\Audit;
use Illuminate\Support\Facades\Config;

class CustomDatabase extends Database
{
	/**
	 * {@inheritdoc}
	 */
	public function audit(Auditable $model): Audit
	{
		$implementation = Config::get('audit.implementation', \OwenIt\Auditing\Models\Audit::class);
		if ($model->auditTable ||
			$model->auditConnection
		) {
			$instance = new $implementation();
			if ($model->auditTable) {
				$instance->setTable($model->auditTable);
			}
			if ($model->auditConnection) {
				$instance->setConnection($model->auditConnection);
			}
			return call_user_func([$instance, 'create'], $model->toAudit());
		}
		return call_user_func([$implementation, 'create'], $model->toAudit());
	}

	/**
	 * Remove older audits that go over the threshold.
	 *
	 * @param \OwenIt\Auditing\Contracts\Auditable $model
	 *
	 * @return bool
	 */
	public function prune(Auditable $model): bool
	{
		if (($threshold = $model->getAuditThreshold()) > 0) {
			$forRemoval = $model->audits()
				->select('id')
				->latest()
				->limit($threshold)
				->get();
			if (!$forRemoval->isEmpty() &&
				$forRemoval->count() >= $threshold
			) {
				return $model->audits()
					->whereNotIn('id', $forRemoval)
					->delete() > 0;
			}
		}

		return false;
	}
}
