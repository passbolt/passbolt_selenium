<?php
/**
 * Bug PASSBOLT-1103 - Regression test
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */
class PASSBOLT1103 extends PassboltTestCase {

	/**
	 * Scenario :   The contextual menu should disappear after changing workspace
	 * Given        I am logged in as Ada on the password workspace
	 * And          I right click on a password
	 * Then         I should see the contextual menu
	 * When         I go to user workspace
	 * Then         I should not see the contextual menu anymore
	 * When         I right click on a user
	 * Then         I should see the contextual menu
	 * When         I go to password workspace
	 * Then         I should not see the contextual menu
	 */
	public function testContextualMenuDisappearAfterChangingWorkspace() {
		// Given I am Ada
		$user = User::get('ada');

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// When I right click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->rightClickPassword($resource['id']);

		// Then I can see the contextual menu
		$this->assertVisible('js_contextual_menu');

		// When I change workspace
		$this->gotoWorkspace( 'user' );

		// Then I shouldn't see the contextual menu anymore
		$this->assertNotVisible('js_contextual_menu');

		// And I right click on user betty
		$betty = User::get( array( 'user' => 'betty' ) );
		$this->rightClickUser($betty['id']);

		// Then I can see the contextual menu
		$this->assertVisible('js_contextual_menu');

		// When I change workspace
		$this->gotoWorkspace( 'password' );

		// Then I shouldn't see the contextual menu anymore
		$this->assertNotVisible('js_contextual_menu');
	}
}