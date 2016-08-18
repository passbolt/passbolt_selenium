<?php
/**
 * Feature : As a user I can comment on a password
 *
 * - As a user I should be able to ad comments
 * - As a user I should see error messages if the content entered is not alright
 * - As a user I should be able to delete a comment
 * - As a user I should receive an email notification when I write a comment.
 *
 * @copyright (c) 2015-present Bolt Softwares Pvt Ltd
 * @licence GNU Affero General Public License http://www.gnu.org/licenses/agpl-3.0.en.html
 */
class PasswordCommentTest extends PassboltTestCase {

	private $commentFormSelector = '#js_rs_details_comments form#js_comment_add_form';

	/**
	 * Scenario :   As a user I should be able to add comments
	 * Given        I am Ada
	 * And          I am logged in
	 * And          I click on a password
	 * Then         I should see the section comments in the sidebar
	 * And          I should see the comment form with a submit button
	 * Given        I am enter a comment in the textearea
	 * And          I click on the send button
	 * Then         I should see the comment being visible in the list
	 * And          I should not see the comment form anymore
	 * Given        I click in the + button in the comments section
	 * Then         I should see the comment form again
	 * Given        I enter another comment in the textarea
	 * And          I click on send button
	 * Then         I should see the new comment in the comments list
	 */
	public function testCommentAdd() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		$comments = [
			'this is a comment',
			'reply to the first comment',
		];

		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace.
		$this->loginAs($user);

		// Make sure the password field is not visible.
		$this->assertNotVisible($this->commentFormSelector);

		// When I click on a password I own.
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Enter comment and post.
		$this->postCommentInSidebar($comments[0]);

		// Make sure the password field is not visible.
		$this->assertNotVisible($this->commentFormSelector);

		// And check that the form is not visible anymore.
		$this->assertVisible('#js_rs_details_comments_list');

		// Check whether the comments list contain the new comment.
		$this->waitUntilISee('#js_rs_details_comments_list', '/' . $comments[0] . '/');

		// Check that the comment date (time ago) is correct.
		$this->waitUntilISee('#js_rs_details_comments_list .modified', '/a few seconds ago/');

		// Click on the + icon to add a new comment.
		$this->assertVisible('#js_rs_details_comments a.section-action');
		$this->click('#js_rs_details_comments a.js_add_comment');

		// Enter and post comment.
		$this->postCommentInSidebar($comments[1]);

		// Check that the 2 comments are visible.
		$this->waitUntilISee('#js_rs_details_comments_list', '/' . $comments[1] . '/');
		$this->assertElementContainsText(
			$this->find('#js_rs_details_comments_list'),
			$comments[0]
		);
	}

	/**
	 * Scenario :   As a user I should see error messages if the content entered is not alright
	 * Given        I am Ada
	 * And          I am logged in
	 * And          I click on a password
	 * Then         I should see the comment form section
	 * When         I click on submit without entering a comment
	 * Then         I should see an error message saying that the information is required
	 * When         I enter text 'aa' in the comment field
	 * Then         I should see an error message 'The content should be between 3 to 255 characters'
	 * When         I enter text 'test<' in the comment field
	 * Then         I should see an error message 'The content should contain only alphabets, numbers and the special characters...'
	 */
	public function testCommentValidate() {
		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// When I click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Make sure password field is visible
		$this->waitUntilISee($this->commentFormSelector);

		// Scroll sidebar to bottom.
		$this->scrollSidebarToBottom();

		// Click on submit.
		$this->click('#js_rs_details_comments a.comment-submit');
		// Then I see an error message saying that the field should not be empty
		$this->assertVisible('#js_rs_details_comments .js_comment_content_feedback');
		$this->assertElementContainsText(
			$this->find('#js_rs_details_comments .js_comment_content_feedback'), 'This information is required'
		);

		$this->inputText('js_field_comment_content', 'aa');
		$this->assertElementContainsText(
			$this->find('#js_rs_details_comments .js_comment_content_feedback'), 'Comment should be between'
		);

		$this->inputText('js_field_comment_content', 'test<');
		$this->assertElementContainsText(
			$this->find('#js_rs_details_comments .js_comment_content_feedback'), 'alphabets, numbers and the special characters'
		);
	}

	/**
	 * Scenario :       As a user I should be able to delete a comment
	 * Given            I am Ada
	 * And              I am logged in
	 * And              I click password
	 * And              I enter and save a comment
	 * Then             I should see the comment in the list
	 * And              I should see a delete button
	 * When             I click on the delete button
	 * Then             I should not see the comment anymore
	 * And              I should see the comment form shown again
	 */
	public function testCommentDelete() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// When I click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Enter comment and submit.
		$this->postCommentInSidebar('this is a test comment');

		// Check whether the comments list contain the new comment.
		$this->waitUntilISee('#js_rs_details_comments_list', '/this is a test comment/');
		$this->assertNotVisible($this->commentFormSelector);

		// Delete comment.
		$buttonDeleteSelector = '#js_rs_details_comments_list a.js_delete_comment';
		$this->assertVisible($buttonDeleteSelector);
		$this->click($buttonDeleteSelector);

		// Assert that the confirmation dialog is displayed.
		$this->assertConfirmationDialog('Do you really want to delete comment ?');

		// Click ok in confirmation dialog.
		$this->confirmActionInConfirmationDialog();

		// Assert delete notification is shown
		$this->assertNotification('app_comments_delete_success');

		// Assert that the comment has disappeared
		$this->assertElementNotContainText(
			$this->find('#js_rs_details_comments_list'),
			'this is a test comment'
		);

		// Check whether the comments list contain the new comment.
		$this->assertElementNotContainText(
			$this->find('#js_rs_details_comments_list'),
			'this is a test comment'
		);
		$this->assertVisible($this->commentFormSelector);
	}

	/**
	 * Scenario :       As a user I should be able to delete a comment
	 * Given            I am Ada
	 * And              I am logged in
	 * And              I click password
	 * And              I enter and save a comment
	 * Then             I should see the comment in the list
	 * And              I should see a delete button
	 * When             I log out and I log in again as betty
	 * And              I select the same password
	 * Then             I should see the comment posted by ada
	 * And              I should not see the delete button
	 */
	public function testCommentDeleteOnlyOwner() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// When I click the password apache
		$this->clickPassword(Uuid::get('resource.id.apache'));

		// Enter and post comment.
		$this->postCommentInSidebar('this is a test comment');

		// I should see the delete button.
		$buttonDeleteSelector = '#js_rs_details_comments_list a.js_delete_comment';
		$this->assertVisible($buttonDeleteSelector);

		// When I logout.
		$this->logout();

		// And I log in again as betty.
		$user = User::get('betty');
		$this->setClientConfig($user);

		$this->loginAs($user);

		// And I select the same apache password.
		$this->clickPassword(Uuid::get('resource.id.apache'));

		// Check whether the comments list contain the new comment.
		$this->waitUntilISee('#js_rs_details_comments_list', '/this is a test comment/');

		// I should not see the delete button.
		$this->assertNotVisible($buttonDeleteSelector);

	}

	/**
	 * Scenario :   As a user I should receive an email notification when I write a comment.
	 * Given        I am Ada
	 * And          I am logged in
	 * And          I click on a password
	 * Then         I should see the section comments in the sidebar
	 * And          I should see the comment form with a submit button
	 * Given        I am enter a comment in the textearea
	 * And          I click on the send button
	 * Then         I should see a notification saying that the comment has been added
	 * When         I access the last email sent to a person the password is shared with (not me)
	 * Then         I should see that the title contains 'myname' commented on 'resourcename'
	 * And          I should see that the email contains the resource name
	 * And          I should see that the email containe the comment content
	 */
	public function testCommentAddEmailNotification() {
		// Reset database at the end of test.
		$this->resetDatabaseWhenComplete();

		// Define comment.
		$comment = 'this is a comment';

		// Given I am Ada
		$user = User::get('ada');
		$this->setClientConfig($user);

		// And I am logged in on the password workspace
		$this->loginAs($user);

		// Make sure the password field is not visible
		$this->assertNotVisible($this->commentFormSelector);

		// When I click on a password I own
		$resource = Resource::get(array('user' => 'ada', 'permission' => 'owner'));
		$this->clickPassword($resource['id']);

		// Enter comment and submit.
		$this->postCommentInSidebar($comment);

		// Access last email sent to Betty.
		$this->getUrl('seleniumTests/showLastEmail/' . urlencode(User::get('betty')['Username']));

		// The email title should be:
		$this->assertMetaTitleContains(sprintf('%s commented on %s', $user['FirstName'], $resource['name']));

		// I should see the resource name in the email.
		$this->assertElementContainsText(
			'bodyTable',
			$user['FirstName'] . ' ' . $user['LastName']
		);

		// I should see the resource name in the email.
		$this->assertElementContainsText(
			'bodyTable',
			$comment
		);

		// I should see the comment in the email
		$this->assertElementContainsText(
			'bodyTable',
			$comment
		);
	}
}