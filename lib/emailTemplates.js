/**
 * Passbolt ~ Open source password manager for teams
 * Copyright (c) Passbolt SA (https://www.passbolt.com)
 *
 * Licensed under GNU Affero General Public License version 3 of the or any later version.
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Passbolt SA (https://www.passbolt.com)
 * @license       https://opensource.org/licenses/AGPL-3.0 AGPL License
 * @link          https://www.passbolt.com Passbolt(tm)
 * @since         v4.2.0
 */

exports.templates = {
  register: {
    AN: {
      registered: "AN/user_register_admin"
    }
  },
  recover: {
    AD: {
      aborted: "AD/setup_recover_abort",
      completed: "AD/recover_complete",
    },
    AN: {
      completed: "AN/user_recover_complete",
      requested: "AN/user_recover",
    }
  },
  resource: {
    LU: {
      created: "LU/resource_create",
      deleted: "LU/resource_delete",
      updated: "LU/resource_update",
      shared: "LU/resource_share",
    }
  },
  folder: {
    LU: {
      created: "Passbolt/Folders.LU/folder_create",
      shared: "Passbolt/Folders.LU/folder_share",
      updated: "Passbolt/Folders.LU/folder_update",
      deleted: "Passbolt/Folders.LU/folder_delete",
    }
  },
  selfRegistration: {
    AD: {
      settingsUpdated: "Passbolt/SelfRegistration.Admin/settings_update",
      userRegistered: "AD/user_register_self",
    },
    AN: {
      userRegistered: "AN/user_register_self"
    }
  },
  accountRecovery: {
    AD: {
      policyDisabled: "Passbolt/AccountRecovery.OrganizationPolicies/disable",
      policyEnabled: "Passbolt/AccountRecovery.OrganizationPolicies/enable",
      policyUpdated: "Passbolt/AccountRecovery.OrganizationPolicies/update",
      badRequest: "Passbolt/AccountRecovery.Requests/bad_request",
      requested: "Passbolt/AccountRecovery.Requests/admin_request",
      responded: "Passbolt/AccountRecovery.Responses/created_admin",
      responsdedAllAdmin: "Passbolt/AccountRecovery.Responses/created_all_admins",
    },
    AN: {
      requested: "Passbolt/AccountRecovery.Responses/user_request",
      approved: "Passbolt/AccountRecovery.Responses/user_approved",
      rejected: "Passbolt/AccountRecovery.Responses/user_rejected",
    }
  },
  mfaPolicies: {
    AD: {
      updated: "Passbolt/MfaPolicies.AD/settings_updated",
    }
  },
  passwordPolicies: {
    AD: {
      updated: "Passbolt/PasswordPoliciesUpdate.AD/settings_updated",
    }
  },
  ssoSettings: {
    AD: {
      activated: "Passbolt/Sso.AD/sso_settings_activated",
      deleted: "Passbolt/Sso.AD/sso_settings_active_deleted",
    }
  },
  setup: {
    AD: {
      completed: "AD/user_setup_complete",
    },
    LU: {
      completed: "LU/user_setup_complete"
    }
  },
  comment: {
    LU: {
      added: "LU/comment_add",
    }
  },
  group: {
    LU: {
      deleted: "LU/group_delete",
      groupUserAdded: "LU/group_user_add",
      groupUserUpdated: "LU/group_user_update",
      groupUserDeleted: "LU/group_user_delete",
    },
    GM: {
      groupUserUpdated: "GM/group_user_update",
      groupUserRequested: "GM/group_user_request",
      groupUserDeleted: "GM/group_user_delete",
    }
  },
  digest: {
    group: {
      LU: {
        groupUsersChanged: "LU/group_users_change",
        groupsDeleted: "LU/groups_delete",
      }
    },
    resource: {
      LU: {
        resourcesChanged: "LU/resources_change",
        resourcesShareed: "LU/resources_share",
      }
    }
  }
};
