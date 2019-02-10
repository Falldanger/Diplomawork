<?php
class adminmenuControllerGmp extends controllerGmp {
    public function sendMailToDevelopers() {
        $res = new responseGmp();
        $data = reqGmp::get('post');
        $fields = array(
            'name' => new fieldGmpGmp('name', __('Your name field is required.'), '', '', 'Your name', 0, array(), 'notEmpty', GMP_LANG_CODE),
            'website' => new fieldGmpGmp('website', __('Your website field is required.'), '', '', 'Your website', 0, array(), 'notEmpty', GMP_LANG_CODE),
            'email' => new fieldGmpGmp('email', __('Your e-mail field is required.'), '', '', 'Your e-mail', 0, array(), 'notEmpty, email', GMP_LANG_CODE),
            'subject' => new fieldGmpGmp('subject', __('Subject field is required.'), '', '', 'Subject', 0, array(), 'notEmpty', GMP_LANG_CODE),
            'category' => new fieldGmpGmp('category', __('You must select a valid category.'), '', '', 'Category', 0, array(), 'notEmpty', GMP_LANG_CODE),
            'message' => new fieldGmpGmp('message', __('Message field is required.'), '', '', 'Message', 0, array(), 'notEmpty', GMP_LANG_CODE),
        );
        foreach($fields as $f) {
            $f->setValue($data[$f->name]);
            $errors = validatorGmp::validate($f);
            if(!empty($errors)) {
                $res->addError($errors);
            }
        }
        if(!$res->error) {
            $msg = 'Message from: '. get_bloginfo('name').', Host: '. $_SERVER['HTTP_HOST']. '<br />';
            foreach($fields as $f) {
                $msg .= '<b>'. $f->label. '</b>: '. nl2br($f->value). '<br />';
            }
			$headers[] = 'From: '. $fields['name']->value. ' <'. $fields['email']->value. '>';
			add_filter('wp_mail_content_type', array(frameGmp::_()->getModule('messenger'), 'mailContentType'));
            wp_mail('support@supsystic.team.zendesk.com', 'Supsystic Easy Google Maps', $msg, $headers);
            $res->addMessage(__('Done', GMP_LANG_CODE));
        }
        $res->ajaxExec();
    }
	/**
	 * @see controller::getPermissions();
	 */
	public function getPermissions() {
		return array(
			GMP_USERLEVELS => array(
				GMP_ADMIN => array('sendMailToDevelopers')
			),
		);
	}
}

