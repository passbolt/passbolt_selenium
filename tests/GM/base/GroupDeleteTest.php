<?php
use Facebook\WebDriver\WebDriverBy;
use Facebook\WebDriver\WebDriverSelect;

/**
 * Feature :  As a group manager I cannot delete groups
 *
 * Scenarios :
 *  - As a group manager I shouldn't be able to delete a group
 *
 * @copyright (c) 2017-present Passbolt SARL
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class GMGroupDeleteTest extends PassboltTestCase {

	/**
	 * Scenario: As a group manager I shouldn't be able to delete a group
	 *
	 * Given    I am logged in as a group manager and I am on the users workspace
	 * When    I click on the contextual menu button of a group on the right
	 * Then    I should see the group contextual menu
	 * And    I should see the “Edit group” option
	 * And    I shouldn't see the "Delete group" option
	 */
	public function testDeleteGroupRightClick() {
		// Given I am logged in as an administrator
		$user = User::get( 'irene' );
		$this->setClientConfig( $user );
		$this->loginAs( $user );
		$this->gotoWorkspace( 'user' );

		// When I click on the contextual menu button of a group on the right
		$groupId = Uuid::get( 'group.id.ergonom' );
		$this->click( "#group_$groupId .right-cell a" );

		// Then I should see the group contextual menu
		$this->assertVisible( '#js_contextual_menu' );
		$this->assertVisible( 'js_group_browser_menu_edit' );
		$this->assertNotVisible( 'js_group_browser_menu_remove' );

		sleep(10);
	}
}