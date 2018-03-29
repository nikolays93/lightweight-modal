<?php


$modal['magnific'] = array(
  array(
    'type' => 'select',
    'id'   => 'modal_type',
    'label'=> 'Библиотека',
    'options' => $modal_types,
    'value' => 'magnific',
  ),
  array(
    'type'      => 'text',
    'id'        => 'magnific',
    'label'     => 'Селектор',
    // 'desc'      => 'Модальное окно (Галерея, всплывающее окно)',
    'placeholder'   => '.magnific, .zoom',
  ),
);