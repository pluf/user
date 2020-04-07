<?php

/**
 * ساختار داده‌ای پروفایل کاربر را تعیین می‌کند.
 * 
 * @author maso <mostafa.barmshory@dpq.co.ir>
 *
 */
class User_Avatar extends Pluf_Model
{

    /**
     * مدل داده‌ای را بارگذاری می‌کند.
     *
     * تمام فیلدهای مورد نیاز برای این مدل داده‌ای در این متد تعیین شده و به
     * صورت کامل ساختار دهی می‌شود.
     *
     * @see Pluf_Model::init()
     */
    function init()
    {
        $this->_a['table'] = 'user_avatars';
        $this->_a['cols'] = array(
            'id' => array(
                'type' => 'Sequence',
                'blank' => true,
                'editable' => false
            ),
            'fileName' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'unique' => false,
                'editable' => false
            ),
            'filePath' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'unique' => false,
                'editable' => false
            ),
            'fileSize' => array(
                'type' => 'Integer',
                'is_null' => false,
                'verbose' => __('validate'),
                'editable' => false
            ),
            'mimeType' => array(
                'type' => 'Varchar',
                'is_null' => false,
                'size' => 50,
                'editable' => false
            ),
            'creationTime' => array(
                'type' => 'Datetime',
                'is_null' => false,
                'editable' => false
            ),
            'modifTime' => array(
                'type' => 'Datetime',
                'is_null' => false,
                'editable' => false
            ),
            /*
             * Foreign key
             */
            'account_id' => array(
                'type' => 'Foreignkey',
                'model' => 'User_Account',
                'unique' => true,
                'name' => 'account',
                'relate_name' => 'avatars',
                'graphql_name' => 'account',
                'is_null' => false,
                'editable' => false
            )
        );

//         $this->_a['idx'] = array(
//             'user_avatar_idx' => array(
//                 'col' => 'account_id',
//                 'type' => 'unique'
//             )
//         );
    }

    /**
     * پیش ذخیره را انجام می‌دهد
     *
     * در این فرآیند نیازهای ابتدایی سیستم به آن اضافه می‌شود. این نیازها
     * مقادیری هستند که
     * در زمان ایجاد باید تعیین شوند. از این جمله می‌توان به کاربر و تاریخ اشاره
     * کرد.
     *
     * @param $create boolean
     *            حالت
     *            ساخت یا به روز رسانی را تعیین می‌کند
     */
    function preSave($create = false)
    {
        if ($this->id == '') {
            $this->creationTime = gmdate('Y-m-d H:i:s');
        }
        $this->modifTime = gmdate('Y-m-d H:i:s');
        // File path
        $path = $this->getAbsloutPath();
        // file size
        if (file_exists($path)) {
            $this->fileSize = filesize($path);
        } else {
            $this->fileSize = 0;
        }
        // mime type (based on file name)
        $fileInfo = Pluf_FileUtil::getMimeType($this->fileName);
        $this->mimeType = $fileInfo[0];
    }

    /**
     * مسیر کامل محتوی را تعیین می‌کند.
     *
     * @return string
     */
    public function getAbsloutPath()
    {
        return $this->filePath . '/' . $this->account_id;
    }

    /**
     *
     * @see Pluf_Model::postSave()
     */
    function preDelete($create = false)
    {
        unlink($this->getAbsloutPath());
    }
}