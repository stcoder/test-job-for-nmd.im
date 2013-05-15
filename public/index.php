<?php
/**
 * @author: Sergey Tihonov
 */
require __DIR__ . '/../core/Response.php';
require __DIR__ . '/../core/View.php';
require __DIR__ . '/../core/Db.php';

$query    = isset($_GET['q']) ? $_GET['q'] : null;
$view     = new View(__DIR__ . '/../templates');
$response = new Response();
$db       = new Db(array(
                    'host'     => 'localhost',
                    'dbname'   => 'z2',
                    'username' => 'mysql',
                    'password' => 'mysql'
                ));

if(
    isset($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    !empty($_SERVER['HTTP_X_REQUESTED_WITH']) &&
    strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest'
    && in_array($query, array('delete', 'edit', 'create', 'save'))) {

    $id   = isset($_REQUEST['id']) ? (int) $_REQUEST['id'] : 0;
    $data = [];

    if ($query != 'create') {
        if ($id <= 0) {
            throw new Exception('Не указан идентификатор услуги');
        }
    }

    // simple routing
    switch($query) {
        case 'delete':
            $data['message'] = 'delete';
            $result = $db->delete('id = ' . $id . ' or parent_id = '  . $id);
            if ($result) {
                // говнокод
                $services = $db->query('SELECT * FROM services');
                ob_start();
                $view->helperServicesList($services, 0, 0);
                $items = ob_get_contents(); ob_end_clean();
                $data = array('deleted' => true, 'items' => $items);
            }
            break;
        case 'edit':
            $rowset = $db->query('SELECT * FROM services WHERE id=' . $id);
            if (count($rowset) > 0) {
                $view->service = $rowset[0];
                $view->services = $db->query('SELECT * FROM services');
                $data['form'] = $view->template('_editForm')->render();
                break;
            }
            throw new Exception('Услуга не найдена');
            break;
        case 'save':
            $rowset = $db->query('SELECT * FROM services WHERE id=' . $id);
            if (count($rowset) <= 0) {
                throw new Exception('Услуга не найдена');
            }
            $row = $rowset[0];
            $parentId = isset($_REQUEST['parent_id']) ? (int) $_REQUEST['parent_id'] : 0;
            $serviceName = isset($_REQUEST['service_name']) ? $_REQUEST['service_name'] : null;
            if (strlen($serviceName) > 255) {
                throw new Exception('Имя сервиса превышает 255 символов');
            }
            $result = $db->update(array(
                                       'id' => $id,
                                       'parent_id' => $parentId,
                                       'name' => $serviceName
                                  ));
            $services = $db->query('SELECT * FROM services');
            // говнокод
            ob_start();
            $view->helperServicesList($services, 0, 0);
            $items = ob_get_contents(); ob_end_clean();
            $data = array('saved' => true, 'items' => $items);
            break;
        case 'create':
            $parentId = isset($_REQUEST['parent_id']) ? (int) $_REQUEST['parent_id'] : 0;
            $serviceName = isset($_REQUEST['service_name']) ? $_REQUEST['service_name'] : null;
            if (strlen($serviceName) > 255) {
                throw new Exception('Имя сервиса превышает 255 символов');
            }
            $result = $db->insert(array(
                             'parent_id' => $parentId,
                             'name' => $serviceName
                        ));
            $data = array('created' => true, 'item' => array(
                'parent_id' => $parentId,
                'id' => $db->lastInsertId(),
                'name' => $serviceName
            ));
            break;
    }

    $response->setContent(json_encode($data));
    $response->setContentType('json');

} else {
    $view->services = $db->query('SELECT * FROM services');
    $view->template('layout');
    $response->setContent($view->render());
}

$response->send();