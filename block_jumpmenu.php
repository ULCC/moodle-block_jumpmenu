<?php
class block_jumpmenu extends block_base {
  function init() {
    $this->title   = get_string('pluginname','block_jumpmenu');
  }

  function has_config() {
      return false;
  }



  function get_content() {

    global $USER, $CFG, $DB, $OUTPUT;
    require_once $CFG->dirroot . '/course/lib.php';

    if ($this->content !== NULL) {
        return $this->content;
    }

    $this->content = new stdClass();
    $this->content->text = '';
    $this->content->footer = '';

    if (empty($this->instance)) {
        return $this->content;
    } else if ($this->page->course->id == SITEID) {
        // return $this->content = '';
    }

    if (!empty($this->page->id)) {
        $context = get_context_instance(CONTEXT_COURSE, $this->page->course->id);
    }

    if (empty($context)) {
        $context = get_context_instance(CONTEXT_SYSTEM);
    }

    if (!$course = $DB->get_record('course', array('id'=>$this->page->course->id))) {
        $course = $SITE;
    }

    if (!has_capability('moodle/course:view', $context)) {  // Just return
        return $this->content;
    }



    if ($course->format == 'topics' || $course->format == 'section') {
       	$format = 'topic';
    }elseif($course->format == 'topcoll'){
        $format = 'ctopics';
    }elseif($course->format == 'weekcoll'){
        $format = 'cweeks';
    }else{
        $format = 'week';
    }
    $current = optional_param($format,-1,PARAM_INT);
    $sections = get_all_sections($course->id);


	foreach($sections as $section) {
		if ($section->visible && $section->section >= 1 && $section->section <= $course->numsections) {
			$summary = $section->summary;
			if (empty($summary)) {
			  $summary = get_string('sectionname',"format_{$course->format}").' '.$section->section;
			}
			if($format == 'week') {
				$sectionmenu[$section->section] = $format.' '.$section->section.' - '.strip_tags($summary);
			}else{
				$sectionmenu[$section->section] = strip_tags($summary);
			}
		}
	}
    $sectionmenu[0] = 'Show All';
	$this->content->text = '<div class="jumpmenu">';
	$this->content->text .= $OUTPUT->single_select(new moodle_url('/course/view.php?id='.$course->id),$format, $sectionmenu, $current, array(''=>get_string('jumpto')),null);
    $this->content->text .= '</div>';

    $this->content->footer = '';

    return $this->content;
  }
}
