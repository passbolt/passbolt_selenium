<?php
/**
 * Feature : Settings Workspace, keys section
 *
 * - As a LU I should be able to see my keys info in the settings workspace, keys section
 * - As a LU I should be able to download my public and private key
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
 */

class SettingsKeyTest extends PassboltTestCase {

  /**
   * Scenario  :  As a LU I should be able to see my keys info in the settings workspace, keys section
   * Given    I am logged in as LU on the settings workspace
   * And      I click on Manage your keys menu
   * Then     I should see the keys section
   * And      The menu "Manage your keys should be selected"
   * And      The breadcrumb should be in this order : 'All users', 'Ada Lovelace', 'Keys management'
   * And      I should see a button Download public key
   * And      I should see a button Download private key
   * And      I should see all the key information (uid, fingerprint, etc)
   * And      I should see a textarea with the public key unarmored inside
   *
   * @throws Exception
   */
	public function testSettingsKeyInfo() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);


		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('settings');

		// Click on Manage your keys.
		$this->clickLink('Manage your keys');

		// I should see a section with a title Profile
		$this->waitUntilISee('.page.settings.keys');

    // Check that menu "Manage your keys" is selected
    $this->assertElementContainsText(
      $this->find('#js_wk_settings_menu .row.selected'),
      'Manage your keys'
    );

    // Check breadcrumb.
    $this->assertBreadcrumb(
      'settings',
      ['All users', 'Ada Lovelace', 'Keys management']
    );

    // I should see a download public key button.
    $this->assertVisible('js_settings_wk_menu_download_public_key');

    // I should see a download private key button.
    $this->assertVisible('js_settings_wk_menu_download_private_key');

    // I should see the uid of the key.
    $this->assertElementContainsText(
      $this->find('#privkeyinfo td.uid'),
      'Ada Lovelace <ada@passbolt.com>'
    );

    // I should see the fingerprint.
    $this->assertElementContainsText(
      $this->find('#privkeyinfo td.fingerprint'),
      '03F60E958F4CB29723ACDF761353B5B15D9B054F'
    );

    // I should see the creation date.
    $this->assertElementContainsText(
      $this->find('#privkeyinfo td.created'),
      '2015-08-09 12:48:31'
    );

    // I should see the expiration date.
    $this->assertElementContainsText(
      $this->find('#privkeyinfo td.expires'),
      '2019-08-09 12:48:31'
    );

    // I should see the length.
    $this->assertElementContainsText(
      $this->find('#privkeyinfo td.length'),
      '4096'
    );

    // I should see the algorithm.
    $this->assertElementContainsText(
      $this->find('#privkeyinfo td.algorithm'),
      'RSA'
    );

    // I should see the the unarmored public key.
    $this->assertElementAttributeEquals(
      $this->find('publicKeyUnarmored'),
      'value',
      file_get_contents(GPG_FIXTURES . DS . $user['PublicKey'])
    );
	}

  /**
   * Scenario :    As a LU I should be able to download my public and private key
   * Given    I am logged in as LU on the settings workspace, keys section
   * And      I click on download public key button
   * Then     the public key should download on my computer
   * And      the downloaded file should contain the corresponding public key
   * When     I click on download private key button
   * Then     the private key should download on my computer
   * And      the downloaded file should contain the corresponding private key
   *
   * @throws Exception
   */
	public function testSettingsKeyDownload() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);


		// And I am logged in on the user workspace
		$this->loginAs($user);

		// Go to user workspace
		$this->gotoWorkspace('settings');

		// Click on Manage your keys.
		$this->clickLink('Manage your keys');

		// I should see a section with a title Profile
		$this->waitUntilISee('.page.settings.keys');

		// Download public key file.
		$this->click('js_settings_wk_menu_download_public_key');

//		// Compare the downloaded file with the key of the user.
//		$md5Downloaded = md5(file_get_contents(SELENIUM_TMP . DS . 'passbolt_public.asc'));
//		$md5ActualKey = md5(file_get_contents(GPG_FIXTURES . DS . $user['PublicKey']));

    // TODO : #PASSBOLT-1253 modify docker container so mounted directories are writeable.
	}

}