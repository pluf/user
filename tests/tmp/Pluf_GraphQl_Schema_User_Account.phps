<?php 
// Import
use GraphQL\Type\Definition\ObjectType;
use GraphQL\Type\Definition\Type;
use GraphQL\GraphQL;
use GraphQL\Type\Schema;
/**
 * Render class of GraphQl
 */
class Pluf_GraphQl_Schema_User_Account { 
    public function render($rootValue, $query) {
        // render object types variables
         $User_Account = null;
         $User_Group = null;
         $User_Role = null;
         $User_Message = null;
         $User_Profile = null;
         $User_Avatar = null;
        // render code
         //
        $User_Account = new ObjectType([
            'name' => 'User_Account',
            'fields' => function () use (&$User_Group, &$User_Role, &$User_Message, &$User_Profile, &$User_Avatar){
                return [
                    // List of basic fields
                    
                    //id: Array(    [type] => Pluf_DB_Field_Sequence    [is_null] => 1    [editable] =>     [readable] => 1)
                    'id' => [
                        'type' => Type::id(),
                        'resolve' => function ($root) {
                            return $root->id;
                        },
                    ],
                    //login: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] =>     [unique] => 1    [size] => 50    [editable] =>     [readable] => 1)
                    'login' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->login;
                        },
                    ],
                    //date_joined: Array(    [type] => Pluf_DB_Field_Datetime    [is_null] => 1    [editable] => )
                    'date_joined' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->date_joined;
                        },
                    ],
                    //last_login: Array(    [type] => Pluf_DB_Field_Datetime    [is_null] => 1    [editable] => )
                    'last_login' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->last_login;
                        },
                    ],
                    //is_active: Array(    [type] => Pluf_DB_Field_Boolean    [is_null] =>     [default] =>     [editable] => )
                    'is_active' => [
                        'type' => Type::boolean(),
                        'resolve' => function ($root) {
                            return $root->is_active;
                        },
                    ],
                    //is_deleted: Array(    [type] => Pluf_DB_Field_Boolean    [is_null] =>     [default] =>     [editable] => )
                    'is_deleted' => [
                        'type' => Type::boolean(),
                        'resolve' => function ($root) {
                            return $root->is_deleted;
                        },
                    ],
                    //Foreinkey value-groups: Array(    [type] => Pluf_DB_Field_Manytomany    [blank] => 1    [model] => User_Group    [relate_name] => accounts    [editable] =>     [graphql_name] => groups    [readable] => 1)
                    'groups' => [
                            'type' => Type::listOf($User_Group),
                            'resolve' => function ($root) {
                                return $root->get_groups_list();
                            },
                    ],
                    //Foreinkey value-roles: Array(    [type] => Pluf_DB_Field_Manytomany    [blank] => 1    [relate_name] => accounts    [editable] =>     [model] => User_Role    [graphql_name] => roles    [readable] => 1)
                    'roles' => [
                            'type' => Type::listOf($User_Role),
                            'resolve' => function ($root) {
                                return $root->get_roles_list();
                            },
                    ],
                    // relations: forenkey 
                    
                    //Foreinkey list-account_id: Array()
                    'messages' => [
                            'type' => Type::listOf($User_Message),
                            'resolve' => function ($root) {
                                return $root->get_messages_list();
                            },
                    ],
                    //Foreinkey list-account_id: Array()
                    'profiles' => [
                            'type' => Type::listOf($User_Profile),
                            'resolve' => function ($root) {
                                return $root->get_profiles_list();
                            },
                    ],
                    //Foreinkey list-account_id: Array()
                    'avatars' => [
                            'type' => Type::listOf($User_Avatar),
                            'resolve' => function ($root) {
                                return $root->get_avatars_list();
                            },
                    ],
                    
                ];
            }
        ]); //
        $User_Group = new ObjectType([
            'name' => 'User_Group',
            'fields' => function () use (&$User_Role, &$User_Account){
                return [
                    // List of basic fields
                    
                    //id: Array(    [type] => Pluf_DB_Field_Sequence    [blank] => 1    [readable] => 1    [editable] => )
                    'id' => [
                        'type' => Type::id(),
                        'resolve' => function ($root) {
                            return $root->id;
                        },
                    ],
                    //name: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] =>     [size] => 50    [verbose] => name)
                    'name' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->name;
                        },
                    ],
                    //description: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] => 1    [size] => 250    [verbose] => description)
                    'description' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->description;
                        },
                    ],
                    //Foreinkey value-roles: Array(    [type] => Pluf_DB_Field_Manytomany    [model] => User_Role    [is_null] => 1    [editable] =>     [relate_name] => groups    [graphql_name] => roles)
                    'roles' => [
                            'type' => Type::listOf($User_Role),
                            'resolve' => function ($root) {
                                return $root->get_roles_list();
                            },
                    ],
                    // relations: forenkey 
                    
                    
                    //Foreinkey list-groups: Array()
                    'accounts' => [
                            'type' => Type::listOf($User_Account),
                            'resolve' => function ($root) {
                                return $root->get_accounts_list();
                            },
                    ],
                ];
            }
        ]); //
        $User_Role = new ObjectType([
            'name' => 'User_Role',
            'fields' => function () use (&$User_Account, &$User_Group){
                return [
                    // List of basic fields
                    
                    //id: Array(    [type] => Pluf_DB_Field_Sequence    [blank] => 1    [editable] =>     [readable] => 1)
                    'id' => [
                        'type' => Type::id(),
                        'resolve' => function ($root) {
                            return $root->id;
                        },
                    ],
                    //name: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] =>     [size] => 50    [verbose] => name)
                    'name' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->name;
                        },
                    ],
                    //description: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] => 1    [size] => 250    [verbose] => description)
                    'description' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->description;
                        },
                    ],
                    //application: Array(    [type] => Pluf_DB_Field_Varchar    [size] => 150    [is_null] =>     [verbose] => application    [help_text] => The application using this permission, for example "YourApp", "CMS" or "SView".)
                    'application' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->application;
                        },
                    ],
                    //code_name: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] =>     [size] => 100    [verbose] => code name    [help_text] => The code name must be unique for each application. Standard permissions to manage a model in the interface are "Model_Name-create", "Model_Name-update", "Model_Name-list" and "Model_Name-delete".)
                    'code_name' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->code_name;
                        },
                    ],
                    // relations: forenkey 
                    
                    
                    //Foreinkey list-roles: Array()
                    'accounts' => [
                            'type' => Type::listOf($User_Account),
                            'resolve' => function ($root) {
                                return $root->get_accounts_list();
                            },
                    ],
                    //Foreinkey list-roles: Array()
                    'groups' => [
                            'type' => Type::listOf($User_Group),
                            'resolve' => function ($root) {
                                return $root->get_groups_list();
                            },
                    ],
                ];
            }
        ]); //
        $User_Message = new ObjectType([
            'name' => 'User_Message',
            'fields' => function () use (&$User_Account){
                return [
                    // List of basic fields
                    
                    //id: Array(    [type] => Pluf_DB_Field_Sequence    [blank] => 1    [editable] =>     [readable] => 1)
                    'id' => [
                        'type' => Type::id(),
                        'resolve' => function ($root) {
                            return $root->id;
                        },
                    ],
                    //Foreinkey value-account_id: Array(    [type] => Pluf_DB_Field_Foreignkey    [model] => User_Account    [name] => account    [graphql_name] => account    [relate_name] => messages    [is_null] =>     [editable] => )
                    'account_id' => [
                            'type' => Type::int(),
                            'resolve' => function ($root) {
                                return $root->account_id;
                            },
                    ],
                    //Foreinkey object-account_id: Array(    [type] => Pluf_DB_Field_Foreignkey    [model] => User_Account    [name] => account    [graphql_name] => account    [relate_name] => messages    [is_null] =>     [editable] => )
                    'account' => [
                            'type' => $User_Account,
                            'resolve' => function ($root) {
                                return $root->get_account();
                            },
                    ],
                    //message: Array(    [type] => Pluf_DB_Field_Text    [is_null] =>     [editable] =>     [readable] => 1)
                    'message' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->message;
                        },
                    ],
                    //creation_dtime: Array(    [type] => Pluf_DB_Field_Datetime    [is_null] => 1    [editable] =>     [readable] => 1)
                    'creation_dtime' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->creation_dtime;
                        },
                    ],
                    // relations: forenkey 
                    
                    
                ];
            }
        ]); //
        $User_Profile = new ObjectType([
            'name' => 'User_Profile',
            'fields' => function () use (&$User_Account){
                return [
                    // List of basic fields
                    
                    //id: Array(    [type] => Pluf_DB_Field_Sequence    [is_null] => 1    [editable] =>     [readable] => 1)
                    'id' => [
                        'type' => Type::id(),
                        'resolve' => function ($root) {
                            return $root->id;
                        },
                    ],
                    //first_name: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] => 1    [size] => 100)
                    'first_name' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->first_name;
                        },
                    ],
                    //last_name: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] =>     [size] => 100)
                    'last_name' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->last_name;
                        },
                    ],
                    //public_email: Array(    [type] => Pluf_DB_Field_Email    [is_null] => 1    [editable] =>     [readable] => 1)
                    'public_email' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->public_email;
                        },
                    ],
                    //language: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] => 1    [default] => en    [size] => 5)
                    'language' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->language;
                        },
                    ],
                    //timezone: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] => 1    [default] => UTC    [size] => 45    [verbose] => time zone    [help_text] => Time zone of the user to display the time in local time.)
                    'timezone' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->timezone;
                        },
                    ],
                    //Foreinkey value-account_id: Array(    [type] => Pluf_DB_Field_Foreignkey    [model] => User_Account    [name] => account    [relate_name] => profiles    [graphql_name] => account    [is_null] =>     [editable] => )
                    'account_id' => [
                            'type' => Type::int(),
                            'resolve' => function ($root) {
                                return $root->account_id;
                            },
                    ],
                    //Foreinkey object-account_id: Array(    [type] => Pluf_DB_Field_Foreignkey    [model] => User_Account    [name] => account    [relate_name] => profiles    [graphql_name] => account    [is_null] =>     [editable] => )
                    'account' => [
                            'type' => $User_Account,
                            'resolve' => function ($root) {
                                return $root->get_account();
                            },
                    ],
                    // relations: forenkey 
                    
                    
                ];
            }
        ]); //
        $User_Avatar = new ObjectType([
            'name' => 'User_Avatar',
            'fields' => function () use (&$User_Account){
                return [
                    // List of basic fields
                    
                    //id: Array(    [type] => Pluf_DB_Field_Sequence    [blank] => 1    [editable] => )
                    'id' => [
                        'type' => Type::id(),
                        'resolve' => function ($root) {
                            return $root->id;
                        },
                    ],
                    //fileName: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] =>     [unique] =>     [editable] => )
                    'fileName' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->fileName;
                        },
                    ],
                    //filePath: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] =>     [unique] =>     [editable] => )
                    'filePath' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->filePath;
                        },
                    ],
                    //fileSize: Array(    [type] => Pluf_DB_Field_Integer    [is_null] =>     [verbose] => validate    [editable] => )
                    'fileSize' => [
                        'type' => Type::int(),
                        'resolve' => function ($root) {
                            return $root->fileSize;
                        },
                    ],
                    //mimeType: Array(    [type] => Pluf_DB_Field_Varchar    [is_null] =>     [size] => 50    [editable] => )
                    'mimeType' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->mimeType;
                        },
                    ],
                    //creationTime: Array(    [type] => Pluf_DB_Field_Datetime    [is_null] =>     [editable] => )
                    'creationTime' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->creationTime;
                        },
                    ],
                    //modifTime: Array(    [type] => Pluf_DB_Field_Datetime    [is_null] =>     [editable] => )
                    'modifTime' => [
                        'type' => Type::string(),
                        'resolve' => function ($root) {
                            return $root->modifTime;
                        },
                    ],
                    //Foreinkey value-account_id: Array(    [type] => Pluf_DB_Field_Foreignkey    [model] => User_Account    [unique] => 1    [name] => account    [relate_name] => avatars    [graphql_name] => account    [is_null] =>     [editable] => )
                    'account_id' => [
                            'type' => Type::int(),
                            'resolve' => function ($root) {
                                return $root->account_id;
                            },
                    ],
                    //Foreinkey object-account_id: Array(    [type] => Pluf_DB_Field_Foreignkey    [model] => User_Account    [unique] => 1    [name] => account    [relate_name] => avatars    [graphql_name] => account    [is_null] =>     [editable] => )
                    'account' => [
                            'type' => $User_Account,
                            'resolve' => function ($root) {
                                return $root->get_account();
                            },
                    ],
                    // relations: forenkey 
                    
                    
                ];
            }
        ]);$rootType =$User_Account;
        try {
            $schema = new Schema([
                'query' => $rootType
            ]);
            $result = GraphQL::executeQuery($schema, $query, $rootValue);
            return $result->toArray();
        } catch (Exception $e) {
            throw new Pluf_Exception_BadRequest($e->getMessage());
        }
    }
}
