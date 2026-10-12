<?php
defined('ABSPATH') || exit;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;
use Elementor\Group_Control_Typography;
use Elementor\Group_Control_Border;
use Elementor\Group_Control_Box_Shadow;
use Elementor\Group_Control_Background;
use Elementor\Responsive_Control; // Added for responsive controls

class CubeWP_Forms_Widget extends Widget_Base
{
    public $change_text;

    public function get_name()
    {
        return 'CubeWP_Forms_Widget';
    }

    public function get_title()
    {
        return __('Cubewp Forms', 'cubewp-forms');
    }

    public function get_icon()
    {
        return 'eicon-form-horizontal';
    }

    public function get_categories()
    {
        return ['cubewp'];
    }

    public function get_keywords()
    {
        return [
            'form',
            'forms',
            'contact form',
            'booking form',
            'reservation form',
            'appointment form',
            'cubewp',
            'submission',
            'input',
            'fields',
            'registration',
            'survey',
            'application',
        ];
    }

    protected function register_controls()
    {
        /*
         * Content Tab
         * -------------------------------------------------
         */
        $this->start_controls_section(
            'section_form_container_content',
            [
                'label' => __('Form Container', 'cubewp-forms'),
                'tab'   => Controls_Manager::TAB_CONTENT,
            ]
        );
        // Select Form Dropdown Control
        $forms = get_posts([
            'numberposts' => -1,
            'fields'      => 'ids',
            'post_type'   => 'cwp_forms',
        ]);

        $forms_options = ['0' => __('Select Form', 'cubewp-forms')];

        if (!empty($forms)) {
            foreach ($forms as $form) {
                $forms_options[$form] = get_post_field('post_title', $form);
            }
        }

        $this->add_control(
            'select_form',
            [
                'label' => __('Select Form', 'cubewp-forms'),
                'type' => Controls_Manager::SELECT,
                'options' => $forms_options,
                'default' => '0',
            ]
        );
        $this->end_controls_section();
        /*
         * Style Tab
         * -------------------------------------------------
         */

        // --- Form Container Styling ---
        $this->start_controls_section(
            'section_form_container_style',
            [
                'label' => __('Form Container', 'cubewp-forms'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // New Controls: Flexbox
        $this->add_responsive_control(
            'form_container_display',
            [
                'label'     => __('Display', 'cubewp-forms'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'block',
                'options'   => [
                    'block'        => __('Block', 'cubewp-forms'),
                    'flex'         => __('Flex', 'cubewp-forms'),
                    'inline-block' => __('Inline Block', 'cubewp-forms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container form' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_container_flex_direction',
            [
                'label'     => __('Flex Direction', 'cubewp-forms'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'column',
                'options'   => [
                    'row'           => __('Row', 'cubewp-forms'),
                    'column'        => __('Column', 'cubewp-forms'),
                    'row-reverse'   => __('Row Reverse', 'cubewp-forms'),
                    'column-reverse' => __('Column Reverse', 'cubewp-forms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container form' => 'flex-direction: {{VALUE}};',
                ],
                'condition' => [
                    'form_container_display' => 'flex',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_container_justify_content',
            [
                'label'     => __('Justify Content', 'cubewp-forms'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'flex-start',
                'options'   => [
                    'flex-start'    => __('Start', 'cubewp-forms'),
                    'flex-end'      => __('End', 'cubewp-forms'),
                    'center'        => __('Center', 'cubewp-forms'),
                    'space-between' => __('Space Between', 'cubewp-forms'),
                    'space-around'  => __('Space Around', 'cubewp-forms'),
                    'space-evenly'  => __('Space Evenly', 'cubewp-forms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container form' => 'justify-content: {{VALUE}};',
                ],
                'condition' => [
                    'form_container_display' => 'flex',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_container_align_items',
            [
                'label'     => __('Align Items', 'cubewp-forms'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'stretch',
                'options'   => [
                    'flex-start' => __('Start', 'cubewp-forms'),
                    'flex-end'   => __('End', 'cubewp-forms'),
                    'center'     => __('Center', 'cubewp-forms'),
                    'stretch'    => __('Stretch', 'cubewp-forms'),
                    'baseline'   => __('Baseline', 'cubewp-forms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container form' => 'align-items: {{VALUE}};',
                ],
                'condition' => [
                    'form_container_display' => 'flex',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'form_container_background',
                'label'    => __('Background', 'cubewp-forms'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'form_container_border',
                'label'    => __('Border', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container',
            ]
        );

        $this->add_control(
            'form_container_border_radius',
            [
                'label'      => __('Border Radius', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'form_container_box_shadow',
                'label'    => __('Box Shadow', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container',
            ]
        );

        $this->add_responsive_control(
            'form_container_padding',
            [
                'label'      => __('Padding', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_container_margin',
            [
                'label'      => __('Margin', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cubewp-selected-form .cwp-frontend-form-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();


        // --- Form Section Content Container Styling ---
        $this->start_controls_section(
            'section_form_section_content_style',
            [
                'label'     => __('Form Section Content', 'cubewp-forms'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'select_form!' => '0',
                ],
            ]
        );

        // **New Control: Form Section Content Background**
        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'form_section_content_background',
                'label'    => __('Background', 'cubewp-forms'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .cwp-frontend-form-container .cwp-frontend-section-content-container',
            ]
        );

        // **New Control: Form Section Content Border**
        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'form_section_content_border',
                'label'    => __('Border', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-frontend-form-container .cwp-frontend-section-content-container',
            ]
        );

        // **New Control: Form Section Content Border Radius**
        $this->add_control(
            'form_section_content_border_radius',
            [
                'label'      => __('Border Radius', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container .cwp-frontend-section-content-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // **New Control: Form Section Content Box Shadow**
        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'form_section_content_box_shadow',
                'label'    => __('Box Shadow', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-frontend-form-container .cwp-frontend-section-content-container',
            ]
        );

        // **New Control: Form Section Content Padding**
        $this->add_responsive_control(
            'form_section_content_padding',
            [
                'label'      => __('Padding', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container .cwp-frontend-section-content-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // **New Control: Form Section Content Margin**
        $this->add_responsive_control(
            'form_section_content_margin',
            [
                'label'      => __('Margin', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container .cwp-frontend-section-content-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Tab Content Styling ---
        $this->start_controls_section(
            'section_tab_content_style',
            [
                'label'     => __('Tab Content', 'cubewp-forms'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'select_form!' => '0',
                ],
            ]
        );

        $this->add_responsive_control(
            'tab_content_width',
            [
                'label'      => __('Width', 'cubewp-forms'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em', 'vw'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 1920,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .cubewp-selected-form .tab-content' => 'width: {{SIZE}}{{UNIT}};',
                ],
            ]
        );
        $this->add_responsive_control(
            'form_fields_margin_content',
            [
                'label'      => __('Margin', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-custom-form .cwp-frontend-section-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Field Container Styling ---
        $this->start_controls_section(
            'section_field_container_style',
            [
                'label'     => __('Field Container', 'cubewp-forms'),
                'tab'       => Controls_Manager::TAB_STYLE,
                'condition' => [
                    'select_form!' => '0',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'field_container_background',
                'label'    => __('Background', 'cubewp-forms'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .cwp-frontend-section-container .cwp-field-container',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'field_container_border',
                'label'    => __('Border', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-frontend-section-container .cwp-field-container',
            ]
        );

        $this->add_control(
            'field_container_border_radius',
            [
                'label'      => __('Border Radius', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-section-container .cwp-field-container' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'field_container_box_shadow',
                'label'    => __('Box Shadow', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-frontend-section-container .cwp-field-container',
            ]
        );

        $this->add_responsive_control(
            'field_container_padding',
            [
                'label'      => __('Padding', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-section-container .cwp-field-container' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'field_container_margin',
            [
                'label'      => __('Margin', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-section-container .cwp-field-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();

        // --- Labels Styling ---
        $this->start_controls_section(
            'section_form_labels_style',
            [
                'label' => __('Labels', 'cubewp-forms'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        // **New Control: Hide/Show Labels**
        $this->add_control(
            'form_labels_display',
            [
                'label'        => __('Show Labels', 'cubewp-forms'),
                'type'         => Controls_Manager::SELECT,
                'options'      => [
                    'block' => __('Show', 'cubewp-forms'),
                    'none'  => __('Hide', 'cubewp-forms'),
                ],
                'default' => __('block', 'cubewp-forms'),
                'selectors'    => [
                    '{{WRAPPER}} .cwp-frontend-form-container label' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'      => 'form_label_typography',
                'label'     => __('Typography', 'cubewp-forms'),
                'selector'  => '{{WRAPPER}} .cwp-frontend-form-container label',
                'condition' => [
                    'form_labels_display' => 'block', // Apply typography only if labels are shown
                ],
            ]
        );

        $this->add_control(
            'form_label_color',
            [
                'label'     => __('Text Color', 'cubewp-forms'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-frontend-form-container label' => 'color: {{VALUE}};',
                ],
                'condition' => [
                    'form_labels_display' => 'block', // Apply color only if labels are shown
                ],
            ]
        );

        $this->add_responsive_control(
            'form_label_margin',
            [
                'label'      => __('Margin', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container label' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'condition'  => [
                    'form_labels_display' => 'block', // Apply margin only if labels are shown
                ],
            ]
        );

        $this->end_controls_section();

        // --- Input Fields & Select Styling ---
        $this->start_controls_section(
            'section_form_fields_style',
            [
                'label' => __('Input & Select Fields', 'cubewp-forms'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'form_fields_typography',
                'label'    => __('Typography', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-frontend-form-container input[type="text"],
                               {{WRAPPER}} .cwp-frontend-form-container input[type="text"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="email"],
                               {{WRAPPER}} .cwp-frontend-form-container input[type="email"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="number"],
                               {{WRAPPER}} .cwp-frontend-form-container input[type="number"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="url"],
                               {{WRAPPER}} .cwp-frontend-form-container input[type="url"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="tel"],
                               {{WRAPPER}} .cwp-frontend-form-container input[type="tel"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="date"],
                               {{WRAPPER}} .cwp-frontend-form-container input[type="date"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="time"],
                               {{WRAPPER}} .cwp-frontend-form-container input[type="time"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container textarea,
                               {{WRAPPER}} .cwp-frontend-form-container textarea::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container select',
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'form_fields_typography_placeholder',
                'label'    => __('Placeholder Typography', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-frontend-form-container input[type="text"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="email"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="number"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="url"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="tel"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="date"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container input[type="time"]::placeholder,
                               {{WRAPPER}} .cwp-frontend-form-container textarea::placeholder',
            ]
        );

        $this->add_control(
            'form_fields_text_color',
            [
                'label'     => __('Text Color', 'cubewp-forms'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="text"]'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="email"]'  => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="number"]' => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="url"]'    => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="tel"]'    => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="date"]'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="time"]'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-frontend-form-container textarea'              => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-frontend-form-container select'                => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_control(
            'form_fields_placeholder_color',
            [
                'label'     => __('Placeholder Color', 'cubewp-forms'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-frontend-form-container input::placeholder'   => 'color: {{VALUE}} !important;',
                    '{{WRAPPER}} .cwp-frontend-form-container textarea::placeholder' => 'color: {{VALUE}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'form_fields_background',
                'label'    => __('Background', 'cubewp-forms'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .cwp-frontend-form-container input[type="text"], {{WRAPPER}} .cwp-frontend-form-container input[type="email"], {{WRAPPER}} .cwp-frontend-form-container input[type="number"], {{WRAPPER}} .cwp-frontend-form-container input[type="url"], {{WRAPPER}} .cwp-frontend-form-container input[type="tel"], {{WRAPPER}} .cwp-frontend-form-container input[type="date"], {{WRAPPER}} .cwp-frontend-form-container input[type="time"], {{WRAPPER}} .cwp-frontend-form-container textarea, {{WRAPPER}} .cwp-frontend-form-container select',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'form_fields_border',
                'label'    => __('Border', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-frontend-form-container input[type="text"], {{WRAPPER}} .cwp-frontend-form-container input[type="email"], {{WRAPPER}} .cwp-frontend-form-container input[type="number"], {{WRAPPER}} .cwp-frontend-form-container input[type="url"], {{WRAPPER}} .cwp-frontend-form-container input[type="tel"], {{WRAPPER}} .cwp-frontend-form-container input[type="date"], {{WRAPPER}} .cwp-frontend-form-container input[type="time"], {{WRAPPER}} .cwp-frontend-form-container textarea, {{WRAPPER}} .cwp-frontend-form-container select',
            ]
        );

        $focus_selector = '{{WRAPPER}} .cwp-frontend-form-container input:focus, {{WRAPPER}} .cwp-frontend-form-container textarea:focus, {{WRAPPER}} .cwp-frontend-form-container select:focus';

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'           => 'form_fields_focus_border',
                'label'          => __('Border (Focus)', 'cubewp-forms'),
                'selector'       => $focus_selector,
                'fields_options' => [
                    'border' => [
                        'selectors' => [
                            '{{SELECTOR}}' => 'border-style: {{VALUE}} !important;',
                        ],
                    ],
                    'width'  => [
                        'selectors' => [
                            '{{SELECTOR}}' => 'border-width: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                        ],
                    ],
                    'color'  => [
                        'selectors' => [
                            '{{SELECTOR}}' => 'border-color: {{VALUE}} !important;',
                        ],
                    ],
                ],
            ]
        );
        $this->add_group_control(
			Group_Control_Background::get_type(),
			[
				'name'     => 'form_fields_background_focus',
				'label'    => __('Background (Focus)', 'cubewp-forms'),
				'types'    => ['classic', 'gradient'],
				'selector' => $focus_selector,
			]
		);
        $this->add_control(
            'form_fields_focus_border_radius',
            [
                'label'      => __('Border Radius (Focus)', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container input:focus'   => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    '{{WRAPPER}} .cwp-frontend-form-container textarea:focus' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    '{{WRAPPER}} .cwp-frontend-form-container select:focus'   => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'           => 'form_fields_focus_box_shadow',
                'label'          => __('Box Shadow (Focus)', 'cubewp-forms'),
                'selector'       => $focus_selector,
                'fields_options' => [
                    'box_shadow' => [
                        'selectors' => [
                            '{{SELECTOR}}' => 'box-shadow: {{HORIZONTAL}}px {{VERTICAL}}px {{BLUR}}px {{SPREAD}}px {{COLOR}} {{box_shadow_position.VALUE}} !important;',
                        ],
                    ],
                ],
            ]
        );

        $this->add_control(
            'form_fields_border_radius',
            [
                'label'      => __('Border Radius', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="text"]'   => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="email"]'  => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="number"]' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="url"]'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="tel"]'    => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="date"]'   => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="time"]'   => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container textarea'              => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container select'                => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        // **New Control: Input Fields Height**
        $this->add_responsive_control(
            'form_fields_height',
            [
                'label'      => __('Height', 'cubewp-forms'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'vh'],
                'range'      => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="text"]'   => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="email"]'  => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="number"]' => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="url"]'    => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="tel"]'    => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="date"]'   => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="time"]'   => 'height: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container select'                => 'height: {{SIZE}}{{UNIT}};',
                    // Note: Textarea height usually controlled by rows or specific height property, this applies to inputs and selects.
                ],
            ]
        );

        $this->add_responsive_control(
            'form_fields_height_textarea',
            [
                'label'      => __('Textarea Height', 'cubewp-forms'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em', 'vh'],
                'range'      => [
                    'px' => [
                        'min' => 20,
                        'max' => 200,
                    ],
                    'em' => [
                        'min' => 1,
                        'max' => 10,
                    ],
                    'vh' => [
                        'min' => 1,
                        'max' => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container textarea'   => 'height: {{SIZE}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_fields_padding',
            [
                'label'      => __('Padding', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="text"]'   => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="email"]'  => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="number"]' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="url"]'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="tel"]'    => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="date"]'   => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container input[type="time"]'   => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container textarea'              => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-frontend-form-container select'                => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_fields_margin',
            [
                'label'      => __('Margin', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-frontend-form-container .cwp-field-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->end_controls_section();
        // --- Submit Button Styling ---
        $this->start_controls_section(
            'section_form_submit_button_style',
            [
                'label' => __('Submit Button', 'cubewp-forms'),
                'tab'   => Controls_Manager::TAB_STYLE,
            ]
        );

        $this->add_responsive_control(
            'form_submit_button_flex',
            [
                'label'     => __('Display', 'cubewp-forms'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'block',
                'options'   => [
                    'block'        => __('Block', 'cubewp-forms'),
                    'flex'         => __('Flex', 'cubewp-forms'),
                    'inline-block' => __('Inline Block', 'cubewp-forms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-form-submit-container  button.cwp-from-submit' => 'display: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_submit_button_justify_content',
            [
                'label'     => __('Justify Content', 'cubewp-forms'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'flex-start',
                'options'   => [
                    'flex-start'    => __('Start', 'cubewp-forms'),
                    'flex-end'      => __('End', 'cubewp-forms'),
                    'center'        => __('Center', 'cubewp-forms'),
                    'space-between' => __('Space Between', 'cubewp-forms'),
                    'space-around'  => __('Space Around', 'cubewp-forms'),
                    'space-evenly'  => __('Space Evenly', 'cubewp-forms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-form-submit-container  button.cwp-from-submit' => 'justify-content: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
            'form_submit_button_align_items',
            [
                'label'     => __('Align Items', 'cubewp-forms'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'stretch',
                'options'   => [
                    'flex-start' => __('Start', 'cubewp-forms'),
                    'flex-end'   => __('End', 'cubewp-forms'),
                    'center'     => __('Center', 'cubewp-forms'),
                    'stretch'    => __('Stretch', 'cubewp-forms'),
                    'baseline'   => __('Baseline', 'cubewp-forms'),
                ],
                'selectors' => [
                    '{{WRAPPER}} .cwp-form-submit-container  button.cwp-from-submit' => 'align-items: {{VALUE}};',
                ],
            ]
        );

        // New: Button Text Control
        $this->add_control(
            'submit_button_text',
            [
                'label'   => __('Button Text', 'cubewp-forms'),
                'type'    => Controls_Manager::TEXT,
                'default' => __('Submit', 'cubewp-forms'),
                'dynamic' => [
                    'active' => true,
                ],
            ]
        );

        // New: Icon Controls
        $this->add_control(
            'submit_button_icon',
            [
                'label'            => __('Icon', 'cubewp-forms'),
                'type'             => Controls_Manager::ICONS,
                'fa4_compatibility' => 'font_awesome',
                'default'          => [
                    'value'   => '',
                    'library' => '',
                ],
            ]
        );

        $this->add_control(
            'submit_button_icon_position',
            [
                'label'     => __('Icon Position', 'cubewp-forms'),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'after',
                'options'   => [
                    'before' => __('Before', 'cubewp-forms'),
                    'after'  => __('After', 'cubewp-forms'),
                ],
                'condition' => [
                    'submit_button_icon[value]!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'submit_button_icon_size',
            [
                'label'      => __('Icon Size', 'cubewp-forms'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 6,
                        'max' => 100,
                    ],
                    'em' => [
                        'min' => 0.5,
                        'max' => 5,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit i'   => 'font-size: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit svg' => 'width: {{SIZE}}{{UNIT}}; height: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'submit_button_icon[value]!' => '',
                ],
            ]
        );

        $this->add_responsive_control(
            'submit_button_icon_spacing',
            [
                'label'      => __('Icon Spacing', 'cubewp-forms'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 50,
                    ],
                    'em' => [
                        'min' => 0,
                        'max' => 3,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit .elementor-button-icon-before' => 'margin-right: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit .elementor-button-icon-after'  => 'margin-left: {{SIZE}}{{UNIT}};',
                ],
                'condition' => [
                    'submit_button_icon[value]!' => '',
                ],
            ]
        );


        // Start Tabs for Normal/Hover states
        $this->start_controls_tabs('tabs_button_style');

        // Normal State Tab
        $this->start_controls_tab(
            'tab_button_normal',
            [
                'label' => __('Normal', 'cubewp-forms'),
            ]
        );

        $this->add_group_control(
            Group_Control_Typography::get_type(),
            [
                'name'     => 'submit_button_typography',
                'label'    => __('Typography', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit',
            ]
        );

        $this->add_control(
            'submit_button_text_color',
            [
                'label'     => __('Text Color', 'cubewp-forms'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit' => 'color: {{VALUE}};',
                ],
            ]
        );

        // New: Icon Color (Normal)
        $this->add_control(
            'submit_button_icon_color',
            [
                'label'     => __('Icon Color', 'cubewp-forms'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit svg' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
                ],
                'condition' => [
                    'submit_button_icon[value]!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submit_button_background',
                'label'    => __('Background', 'cubewp-forms'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'submit_button_border',
                'label'    => __('Border', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit',
            ]
        );

        $this->add_control(
            'submit_button_border_radius',
            [
                'label'      => __('Border Radius', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'submit_button_box_shadow',
                'label'    => __('Box Shadow', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit',
            ]
        );

        $this->end_controls_tab(); // End Normal State Tab

        // Hover State Tab
        $this->start_controls_tab(
            'tab_button_hover',
            [
                'label' => __('Hover', 'cubewp-forms'),
            ]
        );

        $this->add_control(
            'submit_button_hover_text_color',
            [
                'label'     => __('Text Color', 'cubewp-forms'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit:hover' => 'color: {{VALUE}};',
                ],
            ]
        );

        // New: Icon Color (Hover)
        $this->add_control(
            'submit_button_hover_icon_color',
            [
                'label'     => __('Icon Color', 'cubewp-forms'),
                'type'      => Controls_Manager::COLOR,
                'selectors' => [
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit:hover i'   => 'color: {{VALUE}};',
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit:hover svg' => 'fill: {{VALUE}}; stroke: {{VALUE}};',
                ],
                'condition' => [
                    'submit_button_icon[value]!' => '',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Background::get_type(),
            [
                'name'     => 'submit_button_hover_background',
                'label'    => __('Background', 'cubewp-forms'),
                'types'    => ['classic', 'gradient'],
                'selector' => '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit:hover',
            ]
        );

        $this->add_group_control(
            Group_Control_Border::get_type(),
            [
                'name'     => 'submit_button_hover_border',
                'label'    => __('Border', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit:hover',
            ]
        );

        $this->add_control(
            'submit_button_hover_border_radius',
            [
                'label'      => __('Border Radius', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', '%', 'em'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit:hover' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
            ]
        );

        $this->add_group_control(
            Group_Control_Box_Shadow::get_type(),
            [
                'name'     => 'submit_button_hover_box_shadow',
                'label'    => __('Box Shadow', 'cubewp-forms'),
                'selector' => '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit:hover',
            ]
        );

        $this->add_control(
            'submit_button_hover_animation',
            [
                'label'    => __('Hover Animation', 'cubewp-forms'),
                'type'     => Controls_Manager::HOVER_ANIMATION,
            ]
        );

        $this->end_controls_tab(); // End Hover State Tab

        $this->end_controls_tabs(); // End Tabs

        // Controls outside of tabs (apply to both normal and hover if not overridden)
        $this->add_responsive_control(
            'submit_button_padding',
            [
                'label'      => __('Padding', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
                ],
                'separator' => 'before',
            ]
        );

        $this->add_responsive_control(
            'submit_button_margin',
            [
                'label'      => __('Margin', 'cubewp-forms'),
                'type'       => Controls_Manager::DIMENSIONS,
                'size_units' => ['px', 'em', '%'],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-form-submit-container' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}} !important;',
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit' => 'margin: 0 !important;width: 100%;',
                ],
            ]
        );

        $this->add_responsive_control(
            'submit_button_container_width',
            [
                'label'      => __('Button Container Width', 'cubewp-forms'),
                'type'       => Controls_Manager::SLIDER,
                'size_units' => ['px', '%', 'em'],
                'range'      => [
                    'px' => [
                        'min' => 0,
                        'max' => 1000,
                    ],
                    '%'  => [
                        'min' => 0,
                        'max' => 100,
                    ],
                ],
                'selectors'  => [
                    '{{WRAPPER}} .cwp-form-submit-container' => 'width: {{SIZE}}{{UNIT}};',
                    '{{WRAPPER}} .cwp-form-submit-container .cwp-from-submit' => 'width: 100%; display: flex; align-items: center; justify-content: center;',
                ],
            ]
        );

        $this->end_controls_section();
    }




    protected function render()
    {
        $settings = $this->get_settings_for_display();


        if (!empty($settings['select_form']) && $settings['select_form'] != '0') {
            add_filter('cubewp/frontend/form/' . $settings['select_form'] . '/button', [$this, 'my_custom_submit_button'], 10, 3);
            echo '<div class="cubewp-selected-form">';
            echo do_shortcode('[cwpCustomForm form_id=' . $settings['select_form'] . ']');
            echo '</div>';
        } else {
            echo '<p>No form selected.</p>';
        }
    }

    /**
     * Callback for the 'cubewp/frontend/form/{form_id}/button' filter.
     * This method customizes the submit button generated by CubeWP.
     *
     * @param string $submitBTN           The default button HTML.
     * @param string $submit_button_title The default button title.
     * @param string $submit_button_class The default button class.
     * @return string The modified button HTML.
     */
    public function my_custom_submit_button($submitBTN, $submit_button_title, $submit_button_class)
    {
        $settings = $this->get_settings_for_display();

        $button_text = !empty($settings['submit_button_text']) ? $settings['submit_button_text'] : $submit_button_title;
        $icon_html   = '';
        $icon_class  = '';

        // Check if an icon is selected
        if (!empty($settings['submit_button_icon']['value'])) {
            ob_start();
            \Elementor\Icons_Manager::render_icon($settings['submit_button_icon'], ['aria-hidden' => 'true']);
            $icon_html = '<span class="elementor-button-icon elementor-button-icon-' . esc_attr($settings['submit_button_icon_position']) . '">' . ob_get_clean() . '</span>';
        }

        $button_content = '';
        if ($settings['submit_button_icon_position'] === 'before') {
            $button_content = $icon_html . '<span class="elementor-button-text">' . esc_html($button_text) . '</span>';
        } else {
            $button_content = '<span class="elementor-button-text">' . esc_html($button_text) . '</span>' . $icon_html;
        }

        // Ensure the button has the necessary class for Elementor styling
        $submit_button_class .= ' elementor-button'; // Add Elementor's default button class for consistency

        $new_button = sprintf(
            '<div class="cwp-form-submit-container"><button type="submit" class="%1$s">%2$s</button></div>',
            esc_attr($submit_button_class),
            $button_content
        );

        return $new_button;
    }
}
