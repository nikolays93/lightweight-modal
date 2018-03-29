<?php

$modal['photoswipe'] = array(
  array(
    'type' => 'select',
    'id'   => 'modal_type',
    'label'=> 'Библиотека',
    'options' => $modal_types,
    'value' => 'photoswipe',
  ),
  array(
    'type'      => 'text',
    'id'        => 'photoswipe',
    'label'     => 'Селектор',
// 'desc'      => 'Модальное окно (Галерея, всплывающее окно)',
    'placeholder'   => '.photoswipe, .zoom',
  ),
);