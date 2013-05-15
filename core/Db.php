<?php
/**
 * @author: Sergey Tihonov
 */

class Db
{
    /**
     * @var array
     */
    private $_defaultDsnOptions = array(
        'host'     => 'localhost',
        'username' => '',
        'password' => '',
        'charset'  => 'utf-8'
    );

    /**
     * @var object|resource|null
     */
    protected $_connection = null;

    /**
     * @param array $options
     */
    public function __construct($options = array())
    {
        $dsn = array_merge($this->_defaultDsnOptions, $options);
        unset($dsn['username']);
        unset($dsn['password']);
        unset($dsn['charset']);

        foreach($options as $key => $value) {
            $dsn[$key] = sprintf('%s=%s', $key, $value);
        }

        $dsn = 'mysql:' . implode(';', $dsn);

        try {
            $this->_connection = new PDO($dsn, $options['username'], $options['password']);
        } catch (PDOException $e) {
            throw new Exception('Database error', $e->getCode(), $e);
        }
    }

    /**
     * @param       $sqlQuery
     *
     * @return mixed
     */
    public function query($sqlQuery)
    {
        $sth = $this->_connection->query($sqlQuery);
        return $sth->fetchAll();
    }

    /**
     * @param array $data
     *
     * @return bool
     */
    public function insert($data = array())
    {
        $data = array_merge(array(
                                'parent_id' => 0,
                                'name' => ''
                           ), $data);
        $sth = $this->_connection->prepare('INSERT INTO services (id, parent_id, name) VALUES(null, :parent_id, :name)');
        return $sth->execute($data);
    }

    /**
     * @param       $id
     * @param array $data
     *
     * @return mixed
     */
    public function update($data = array())
    {
        $data = array_merge(array(
                                 'id' => 0,
                                 'parent_id' => 0,
                                 'name' => ''
                            ), $data);
        $sth = $this->_connection->prepare('UPDATE services SET parent_id = :parent_id, name = :name WHERE id = :id');
        return $sth->execute($data);
    }

    /**
     * @return mixed
     */
    public function lastInsertId()
    {
        return $this->_connection->lastInsertId();
    }

    /**
     * @param $where
     *
     * @return mixed
     */
    public function delete($where)
    {
        $sth = $this->_connection->query('delete from services where ' . $where);
        return $sth->execute();
    }
}