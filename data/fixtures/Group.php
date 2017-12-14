<?php
/**
 * Groups fixture.
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
namespace Data\Fixtures;

use App\Lib\UuidFactory;

class Group {
	/**
	 * @return array
	 */
	static function _get() {
		$g = [];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.sales'),
			'name' => 'Sales',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.it_support'),
			'name' => 'IT support',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.management'),
			'name' => 'Management',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.human_resource'),
			'name' => 'Human resource',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.creative'),
			'name' => 'Creative',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.operations'),
			'name' => 'Operations',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.accounting'),
			'name' => 'Accounting',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.leadership_team'),
			'name' => 'Leadership team',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.developer'),
			'name' => 'Developer',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.quality_assurance'),
			'name' => 'Quality assurance',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.traffic'),
			'name' => 'Traffic',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.freelancer'),
			'name' => 'Freelancer',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.ergonom'),
			'name' => 'Ergonom',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.board'),
			'name' => 'Board',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.marketing'),
			'name' => 'Marketing',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.resource_planning'),
			'name' => 'Resource planning',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.procurement'),
			'name' => 'Procurement',
		];
		$g[] = [
			'id' =>  UuidFactory::uuid('group.id.network'),
			'name' => 'Network',
		];

		return $g;
	}

	/**
	 * Get one group by it's UUID
	 * @param $g array groups
	 * @param $id
	 * @return mixed
	 */
	static function _getById($g, $id) {
		foreach ($g as $i => $rid) {
			if ($g[$i]['id'] == $id) {
				return $g[$i];
			}
		}
		return false;
	}

	/**
	 * Return one group based on the given conditions
     *
	 * @param array $conditions
	 * @return array $group
	 */
	static function get($conditions = array()) {
		$g = self::_get();

		// filter by id if needed
		if (isset($conditions['id'])) {
			$g = self::_getById($g, $conditions['id']);
		}

		if ($g === false) {
		    \PHPUnit_Framework_Assert::fail('a group fixture could not be found for these conditions, consider adding one');
		}
		return $g;
	}
}
