<?php
if(!defined('WPINC')) {
	die;
}

/**
 * Adds Foo_Widget widget.
 */
class LV_Widget extends WP_Widget {

	private $items;

	/**
	 * Register widget with WordPress.
	 */
	public function __construct() {
		parent::__construct(
	 		'linkview_widget', // Base ID
			'LinkView', // Name
			array('description' => __('This widget allows you to insert the linkview shortcode in the sidebar. You can set every attribute which is available for the shortcode.', 'text_domain'),) // Args
		);
		// define all available items
		$this->items = array(
			'title' => array('type'          => 'text',
			                 'std_value'     => __('Links', 'text_domain'),
			                 'caption'       => __('Title:'),
			                 'tooltip'       => __('The title for the widget'),
			                 'form_style'    => null),

			'atts' =>  array('type'          => 'textarea',
			                 'std_value'     => '',
			                 'caption'       => __('Shortcode attributes:'),
			                 'tooltip'       => __('You can add all attributes which are available for the linkview shortcode'),
			                 'form_style'    => null,
			                 'form_rows'     => 5)
		);
	}

	/**
	 * Front-end display of widget.
	 *
	 * @see WP_Widget::widget()
	 *
	 * @param array $args     Widget arguments.
	 * @param array $instance Saved values from database.
	 */
	public function widget($args, $instance) {
		$title = apply_filters('widget_title', $instance['title']);
		$out = $args['before_widget'];
		if(!empty($title)) {
			$out .= $args['before_title'].$title.$args['after_title'];
		}
		$out .= do_shortcode('[linkview '.$instance['atts'].']');
		$out .= $args['after_widget'];
		echo $out;
	}

	/**
	 * Sanitize widget form values as they are saved.
	 *
	 * @see WP_Widget::update()
	 *
	 * @param array $new_instance Values just sent to be saved.
	 * @param array $old_instance Previously saved values from database.
	 *
	 * @return array Updated safe values to be saved.
	 */
	public function update($new_instance, $old_instance) {
		$instance = array();
		foreach($this->items as $itemname => $item) {
			$instance[$itemname] = strip_tags($new_instance[$itemname]);
		}
		return $instance;
	}

	/**
	 * Back-end widget form.
	 *
	 * @see WP_Widget::form()
	 *
	 * @param array $instance Previously saved values from database.
	 */
	public function form($instance) {
		$out = '';
		foreach($this->items as $itemname => $item) {
			if(!isset($instance[$itemname])) {
				$instance[$itemname] = $item['std_value'];
			}
			$style_text = (null===$item['form_style']) ? '' : ' style="'.$item['form_style'].'"';
			if('textarea' === $item['type']) {
				$rows = (isset($item['form_rows']) && null===$item['form_rows']) ? '' : ' rows='.$item['form_rows'];
				$out .= '
					<p'.$style_text.' title="'.$item['tooltip'].'">
						<label for="'.$this->get_field_id($itemname).'">'.$item['caption'].' </label>
						<textarea class="widefat" id="'.$this->get_field_id($itemname).'" name="'.$this->get_field_name($itemname).'"'.$rows.'>'.esc_attr($instance[$itemname]).'</textarea>
					</p>';
			}
			else { // 'text'
				$out .= '
					<p'.$style_text.' title="'.$item['tooltip'].'">
						<label for="'.$this->get_field_id($itemname).'">'.$item['caption'].' </label>
						<input class="widefat" id="'.$this->get_field_id($itemname).'" name="'.$this->get_field_name($itemname).'" type="text" value="'.esc_attr($instance[$itemname]).'" />
					</p>';
			}
		}
		echo $out;
	}
} // end of class LV_Widget
?>
