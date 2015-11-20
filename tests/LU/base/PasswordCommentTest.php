<?php
/**
 * Feature : As a user I can comment on a password
 *
 * - As a user I should be able to ad comments
 * - As a user I should see error messages if the content entered is not alright
 * - As a user I should be able to delete a comment
 *
 * @copyright    (c) 2015-present Bolt Software Pvt. Ltd.
 * @licence      GPLv3 onwards www.gnu.org/licenses/gpl-3.0.en.html
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
		$comments = [
			'this is a comment',
			'reply to the first comment',
		];

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

		// Make sure password field is visible
		$this->waitUntilISee($this->commentFormSelector);

		// Fill up a first comment
		$this->inputText('js_field_comment_content', $comments[0]);

		// Click on submit.
		$this->click('#js_rs_details_comments a.comment-submit');

		// Assert that notification is shown
		$this->assertNotification('app_comments_addforeigncomment_success');

		// Make sure the password field is not visible
		$this->assertNotVisible($this->commentFormSelector);

		// And check that the form is not visible anymore.
		$this->assertVisible('#js_rs_details_comments_list');

		// Check whether the comments list contain the new comment.
		$this->waitUntilISee('#js_rs_details_comments_list', '/' . $comments[0] . '/');

		// Click on the + icon to add a new comment
		$this->assertVisible('#js_rs_details_comments a.section-action');
		$this->click('#js_rs_details_comments .section-action i.fa-plus-circle');

		// Make sure password field is visible again.
		$this->assertVisible($this->commentFormSelector);
		// Fill up a second comment
		$this->inputText('js_field_comment_content', $comments[1]);
		// Click on submit.
		$this->click('#js_rs_details_comments a.comment-submit');

		// Check that the 2 comments are visible.
		$this->waitUntilISee('#js_rs_details_comments_list', '/' . $comments[1] . '/');
		$this->assertElementContainsText(
			$this->find('#js_rs_details_comments_list'),
			$comments[0]
		);

		$this->resetDatabase();
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

		// Fill up a first comment
		$this->inputText('js_field_comment_content', 'this is a test comment');

		// Click on submit.
		$this->click('#js_rs_details_comments a.comment-submit');

		// Check whether the comments list contain the new comment.
		$this->waitUntilISee('#js_rs_details_comments_list', '/this is a test comment/');
		$this->assertNotVisible($this->commentFormSelector);

		// Delete comment.
		$buttonDeleteSelector = '#js_rs_details_comments_list a .icon.delete';
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

		$this->resetDatabase();
	}
}