<?php
/*
Author: Lee Braiden
Author URI: http://www.kintassa.com
Copyright: Copyright (c) 2011 Kintassa.
License: All rights reserved.  Contact Kintassa should you wish to license this product.
*/

require_once(kintassa_core('kin_form.php'));
require_once(kin_accord_inc('config.php'));
require_once(kin_accord_inc('accordion.php'));

abstract class KintassaAccordionPanelForm extends KintassaForm {
	function __construct($name, $default_vals) {
		parent::__construct($name);
		$this->add_widgets($default_vals);
	}

	function add_widgets($def) {
		$this->sort_pri_field = new KintassaIntegerField(
			"Sort Priority", $name="sort_pri",
			$default_value=$def['sort_pri'], $required=true
		);
		$this->add_child($this->sort_pri_field);

		$this->name_band = new KintassaFieldBand("nameband");
		$this->name_field = new KintassaTextField(
			"Name", $name="name",
			$default_value = $def['name'], $required=true
		);
		$this->name_band->add_child($this->name_field);
		$this->add_child($this->name_band);

		$this->accordion_id_field = new KintassaHiddenField(
			"Accordion ID", $name="accordion_id",
			$default_value = $def['accordion_id'], $required=true
		);

		if (array_key_exists('title', $def)) {
			$title = $def['title'];
		} else {
			$title = '';
		}
		$this->title_band = new KintassaFieldBand("titleband");
		$this->title_field = new KintassaTextAreaField(
			"Title", $name="title",
			$default_value = $title, $required = false
		);
		$this->title_band->add_child($this->title_field);
		$this->add_child($this->title_band);

		if (array_key_exists('content', $def)) {
			$content = $def['content'];
		} else {
			$content = '';
		}
		$this->content_band = new KintassaFieldBand("contentband");
		$this->content_field = new KintassaTextAreaField(
			"Content", $name="content",
			$default_value = $content, $required = false
		);
		$this->content_band->add_child($this->content_field);
		$this->add_child($this->content_band);

		$button_bar = new KintassaFieldBand("button_bar");
		$confirm_button = new KintassaButton(
			"Confirm", $name="confirm", $primary = true
		);
		$button_bar->add_child($confirm_button);
		$this->add_child($button_bar);
	}

	function accordion_return_link() {
		$edit_args = array("mode" => "accordion_edit", "id" => $this->accordion_id_field->value());
		$edit_uri = KintassaUtils::admin_path("KintassaAccordionsMenu", "mainpage", $edit_args);
		echo ("<a href=\"$edit_uri\">" . __("Return to accordion") . "</a>");
	}

	function data() {
		$dat = array(
			"sort_pri"				=> $this->sort_pri_field->value(),
			"name"					=> $this->name_field->value(),
			"title"					=> $this->title_field->value(),
			"content"				=> $this->content_field->value(),
			"accordion_id"			=> (int) $this->accordion_id_field->value(),
		);

		return $dat;
	}

	function data_format() {
		$fmt = array(
			"%d",
			"%s",
			"%s",
			"%s",
			"%d"
		);
		return $fmt;
	}

	// update the record in the database, based on the form details
	abstract function update_record();

	function render($as_sub_el = false) {
		parent::render($as_sub_el);

		$this->accordion_return_link();
	}

	function is_valid() {
		if (!parent::is_valid()) return false;
		return $this->buttons_submitted(array('confirm')) != null;
	}

	function handle_submissions() {
		$res = $this->update_record();
		if ($res) {
			$this->render_success();
		}

		return $res;
	}
}

?>
