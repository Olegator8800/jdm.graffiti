jdm.graffiti

����������:
    - ������ php >=5.4
    - ��� ������ 12 � ������
    - ��� ��������� ������������� ���� ������ define("BX_COMP_MANAGED_CACHE", true);
    - imagick ��� �������� ����������

������ �����������:

    $APPLICATION->IncludeComponent(
        'jdm:jdm.graffiti',
        '',
        array(
            'COMPONENT_TEMPLATE' => '.default',
            'PICTURE_DIR_PATH' => '/graffiti/',
            'SEF_MODE' => 'Y',
            'SEF_FOLDER' => '/test/',
            'CACHE_TYPE' => 'A',
            'CACHE_TIME' => '3600',
            'SEF_URL_TEMPLATES' => array(
                'index' => 'index.php',
                'new' => 'new',
                'show' => '#ELEMENT_ID#',
                'edit' => '#ELEMENT_ID#/edit',
            ),
        )
    );

