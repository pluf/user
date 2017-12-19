<?php
return array (
    
    'User' => array(
        'relate_to_many' => array(
            'Group',
            'Role'
        ),
        
    ),
    'User_Message' => array(
        'relate_to' => array(
            'User'
        )
    ),
);