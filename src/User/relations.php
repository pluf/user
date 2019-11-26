<?php
return array (
    
    'User_Account' => array(
        'relate_to_many' => array(
            'User_Group',
            'User_Role'
        ),
        
    ),
    'User_Message' => array(
        'relate_to' => array(
            'User_Account'
        )
    ),
    'User_Profile' => array(
        'relate_to' => array(
            'User_Account'
        )
    ),
    'User_Avatar' => array(
        'relate_to' => array(
            'User_Account'
        )
    ),
    'User_Verification' => array(
        'relate_to' => array(
            'User_Account'
        )
    ),
    'User_Email' => array(
        'relate_to' => array(
            'User_Account'
        )
    ),
    'User_Phone' => array(
        'relate_to' => array(
            'User_Account'
        )
    )
);