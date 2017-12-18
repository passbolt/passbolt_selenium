<?php
/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SARL (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright Copyright (c) Passbolt SARL (https://www.passbolt.com)
 * @license   https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link      https://www.passbolt.com Passbolt(tm)
 * @since     2.0.0
 */
namespace App\Assertions;

use Facebook\WebDriver\Remote\RemoteWebDriver;
use Facebook\WebDriver\WebDriverBy;
use App\Lib\UuidFactory;

trait WorkspaceAssertionsTrait
{
    abstract public function getDriver(): RemoteWebDriver;

    /**
     * Check that the breadcumb contains the given crumbs
     *
     * @param string $wspName the workspace name
     * @param array $crumbs the crumbs to check
     */
    public function assertBreadcrumb($wspName, $crumbs) 
    {
        // Find the breadcrumb element.
        $id = 'js_wsp_' . $wspName . '_breadcrumb';
        $breadcrumbElement = $this->getDriver()->findElement(WebDriverBy::id($id));
        // Check that the breadcrumb element contains the given crumbs.
        for ($i=0; $i< count($crumbs); $i++) {
            $this->assertElementContainsText(
                $breadcrumbElement,
                $crumbs[$i]
            );
        }
    }

    /**
     * Check if a notification is displayed
     *
     * @see   in passbolt/app/webroot/js/app/config/notification.json for notification uuid seed
     *         example: UuidFactory::uuid('app_resources_index_success') is how to create the id from the seed
     * @param $notificationId
     * @param string         $msg
     */
    public function assertNotification($notificationId)
    {
//        $notificationId = '#notification_' . UuidFactory::uuid($notificationId);
        $this->waitUntilISee('#js_app_notificator .' . $notificationId);
    }

}