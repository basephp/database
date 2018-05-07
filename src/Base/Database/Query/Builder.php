<?php

namespace Base\Database\Query;

use Base\Support\Collection;
use Base\Database\ConnectionInterface;


/**
* Class Builder
*
*/
class Builder
{

    /**
     * The database connection instance.
     *
     * @var \Base\Database\Connection
     */
    public $connection;

    /**
    * SELECT
    *
    * @var array
    */
    protected $select = [];


    /**
    * FROM
    *
    * @var array
    */
    protected $from = [];


    /**
    * JOIN
    *
    * @var array
    */
    protected $join = [];


    /**
    * WHERE
    *
    * @var array
    */
    protected $where = [];


    /**
    * GROUP BY
    *
    * @var array
    */
    protected $group = [];


    /**
    * ORDER BY
    *
    * @var array
    */
    public $order = [];


    /**
    * HAVING
    *
    * @var array
    */
    protected $having = [];


    /**
    * LIMIT
    *
    * @var int
    */
    protected $limit = false;


    /**
    * OFFSET
    *
    * @var int
    */
    protected $offset = false;


    /**
    * DISTINCT
    *
    * @var bool
    */
    protected $distinct = false;


    /**
    * SET
    *
    * @var array
    */
    protected $set = [];


    /**
     * New query builder instance.
     *
     * @return void
     */
    public function __construct($connection)
    {
        $this->connection = $connection;
    }


    /**
    * FROM
    *
    * @param mixed $from
    * @return $this
    */
    public function from($from)
    {
        if (is_string($from))
        {
            $from = explode(',', $from);
        }

        foreach ($from as $val)
        {
            $val = trim($val);

            if ($val !== '' && !in_array($val, $this->from))
            {
                $this->from[] = $val;
            }
        }

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * SELECT
    *
    * @param mixed $select
    * @return $this
    */
    public function select($select = '*')
    {
        if (is_string($select))
        {
            $select = explode(',', $select);
        }

        foreach ($select as $val)
        {
            $val = trim($val);

            if ($val !== '' && !in_array($val, $this->select))
            {
                $this->select[] = $val;
            }
        }

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * JOIN
    *
    *
    * @param string $table
    * @param string $condition
    * @param string $type
    *
    * @return $this
    */
    public function join($table, $condition, $type = '')
    {
        if ($type !== '')
        {
            $type = strtoupper(trim($type));

            if ( ! in_array($type, ['LEFT', 'RIGHT', 'OUTER', 'INNER', 'LEFT OUTER', 'RIGHT OUTER'], true))
            {
                $type = '';
            }
        }

        $this->join[] = [
            'table' => $table,
            'type' => $type,
            'condition' => $condition
        ];

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * WHERE
    *
    * @param mixed $key
    * @param mixed $value
    * @return $this
    */
    public function where($key, $value = NULL)
    {
        if ( ! is_array($key))
        {
            $key = [$key => $value];
        }

        // always default to "=" operator
        $o = '=';

        foreach ($key as $k => $v)
        {
            if ($v !== NULL)
            {
                $op = $this->getOperator($k);
                $k  = trim(str_replace($op, '', $k));

                if (!empty($op))
                {
                    $o = $op;
                }
            }

            $this->where[] = [
                'f' => $k,
                'o' => $o,
                'v' => ($v !== NULL) ? $this->escape($v) : NULL
            ];

        }

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * HAVING
    *
    * @param mixed $key
    * @param mixed $value
    * @return $this
    */
    public function having($key, $value = NULL)
    {
        if ( ! is_array($key))
        {
            $key = [$key => $value];
        }

        foreach ($key as $k => $v)
        {
            if ($v !== NULL)
            {
                $op = $this->getOperator($k);
                $k  = trim(str_replace($op, '', $k));

                if (!$op) continue;
            }

            $this->having[] = [
                'f' => $k,
                'o' => $op ?? '',
                'v' => ($v !== NULL) ? $this->escape($v) : NULL
            ];

        }

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * WHERE IN (...)
    *
    * @param string $key
    * @param array $values
    * @return $this
    */
    public function in($key, $values = [])
    {
        $values = (array) $values;

        if (!is_string($key))
        {
            return $this;
        }

        $vals = [];
        foreach($values as $v)
        {
            if (!is_string($v) && !is_numeric($v)) continue;

            $vals[] = (is_string($v) ? $this->escape($v) : $v);
        }

        $this->where[] = [
            'f' => $key,
            'o' => 'IN',
            'v' => $vals
        ];

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * WHERE NOT IN (...)
    *
    * @param string $key
    * @param array $values
    * @return $this
    */
    public function not($key, $values = [])
    {
        $values = (array) $values;

        if (!is_string($key))
        {
            return $this;
        }

        $vals = [];
        foreach($values as $v)
        {
            if (!is_string($v) && !is_numeric($v)) continue;

            $vals[] = (is_string($v) ? $this->escape($v) : $v);
        }

        $this->where[] = [
            'f' => $key,
            'o' => 'NOT IN',
            'v' => $vals
        ];

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * ORDER BY
    *
    * @param mixed $orderby
    * @param string $direction
    * @return $this
    */
    public function order($orderby, $direction = '')
    {
        if ($direction != '')
        {
            $direction = strtoupper(trim($direction));
            $direction = in_array($direction, ['ASC', 'DESC'], true) ? $direction : '';
        }

        if (empty($orderby))
        {
            return $this;
        }

        if (is_string($orderby))
        {
            $orderby = explode(',', $orderby);
        }

        foreach ($orderby as $field)
        {
            $field = trim($field);

            if ($field !== '')
            {
                $val = preg_match('/\s+(ASC|DESC)$/i', rtrim($field), $match, PREG_OFFSET_CAPTURE);

                $this->order[] = [
                    'field' => ($val) ? ltrim(substr($field, 0, $match[0][1])) : $field,
                    'dir' => ($val) ? $match[1][0] : $direction
                ];
            }
        }

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * GROUP BY
    *
    * @param mixed $groupby
    * @return $this
    */
    public function group($groupby)
    {
        if (is_string($groupby))
        {
            $groupby = explode(',', $groupby);
        }

        foreach ($groupby as $val)
        {
            $val = trim($val);

            if ($val !== '')
            {
                $this->group[] = $val;
            }
        }

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * LIMIT
    *
    * @param int $limit
    * @param int $offset
    * @return $this
    */
    public function limit(int $limit = null, int $offset = 0)
    {
        if ( ! is_null($limit))
        {
            $this->limit = $limit;
        }

        if ( ! empty($offset))
        {
            $this->offset = $offset;
        }

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * OFFSET
    *
    * @param int $offset
    * @return $this
    */
    public function offset($offset)
    {
        if ( ! empty($offset))
        {
            $this->offset = (int) $offset;
        }

        return $this;
    }


    //--------------------------------------------------------------------


    /**
	 * DISTINCT
	 *
	 * Sets the SQL to SELECT DISTINCT,
	 *
	 * @param bool $set
	 * @return $this
	 */
	public function distinct($set = true)
	{
		$this->distinct = is_bool($set) ? $set : true;

		return $this;
	}


    //--------------------------------------------------------------------


    /**
    * SET
    *
    * @param mixed $set
    * @param mixed $value
    * @return $this
    */
    protected function set($set, $value = NULL)
    {
        if (!is_array($set) && !is_null($value))
        {
            $set = [$set => $value];
        }

        foreach ($set as $k => $v)
        {
            $v = trim($v);

            if ($v !== '')
            {
                $this->set[$k] = $this->escape($v);
            }
        }

        return $this;
    }


    //--------------------------------------------------------------------


    /**
    * Run the query and return the results
    *
    */
    public function get($test = false)
    {
        $sql = $this->buildSelect();

        if ($test==true) return $sql;

        return (new Collection($this->connection->query($sql)->results())) ?? false;
    }


    //--------------------------------------------------------------------


    /**
    * Run the query and return the FIRST result
    *
    */
    public function first($test = false)
    {
        $sql = $this->buildSelect();

        if ($test==true) return $sql;

        return $this->connection->query($sql)->results()[0] ?? false;
    }


    //--------------------------------------------------------------------


    /**
    * Run the query and return the LAST result
    *
    */
    public function last($test = false)
    {
        $sql = $this->buildSelect();

        if ($test==true) return $sql;

        return array_slice($this->connection->query($sql)->results(),-1)[0] ?? false;
    }


    //--------------------------------------------------------------------


    /**
    * Run the query and return the results
    *
    */
    public function update($set = [], $test = false)
    {
        if (empty($this->from) || !$this->from) return false;
        if (empty($set)) return false;

        $this->set($set);

        $sql = "UPDATE ".implode(',', $this->from);
        $sql = $this->sqlSet($sql);
        $sql = $this->sqlWhere($sql);
        $sql = $this->sqlLimit($sql);

        if ($test==true) return $sql;

        return $this->connection->query($sql);
    }


    //--------------------------------------------------------------------


    /**
    * Run the query and return the results
    *
    */
    public function insert($set = [], $test = false)
    {
        if (empty($this->from) || !$this->from) return false;
        if (empty($set)) return false;

        $this->set($set);

        $sql = "INSERT INTO ".implode(',', $this->from);
        $sql = $this->sqlSet($sql);

        if ($test==true) return $sql;

        $r = $this->connection->query($sql);

        if ($r)
        {
            return $this->connection->insertId();
        }

        return false;
    }


    //--------------------------------------------------------------------


    /**
    * Run the query and return the results
    *
    */
    public function increment($field, $number, $test = false)
    {
        return $this->eqmath($field, $number, '+', $test);
    }


    //--------------------------------------------------------------------


    /**
    * Run the query and return the results
    *
    */
    public function decrement($field, $number, $test = false)
    {
        return $this->eqmath($field, $number, '-', $test);
    }


    //--------------------------------------------------------------------


    /**
    * Run the query and return the results
    *
    */
    public function eqmath($field, $number, $operator = '+', $test = false)
    {
        if (empty($this->from) || !$this->from) return false;

        $sql  = "UPDATE ".implode(',', $this->from);
        $sql .= " SET ".$field." = ".$field.$operator.$number." ";

        $sql  = $this->sqlWhere($sql);
        $sql  = $this->sqlLimit($sql);

        if ($test==true) return $sql;

        return $this->connection->query($sql);
    }


    //--------------------------------------------------------------------


    /**
    * Run the query and return the results
    *
    */
    public function delete($test = false)
    {
        if (empty($this->from) || !$this->from) return false;

        $sql = "DELETE FROM ".implode(',', $this->from);
        $sql = $this->sqlWhere($sql);
        $sql = $this->sqlLimit($sql);

        if ($test==true) return $sql;

        return $this->connection->query($sql);
    }


    //--------------------------------------------------------------------


    /**
    * count
    *
    */
    public function count()
    {
        return $this->eqnumber("*", 'COUNT');
    }


    //--------------------------------------------------------------------


    /**
    * avg
    *
    */
    public function avg($field)
    {
        return $this->eqnumber($field, 'AVG');
    }


    //--------------------------------------------------------------------


    /**
    * max
    *
    */
    public function max($field)
    {
        return $this->eqnumber($field, 'MAX');
    }


    //--------------------------------------------------------------------


    /**
    * min
    *
    */
    public function min($field)
    {
        return $this->eqnumber($field, 'MIN');
    }


    //--------------------------------------------------------------------


    /**
    * sum
    *
    */
    public function sum($field)
    {
        return $this->eqnumber($field, 'SUM');
    }


    //--------------------------------------------------------------------


    /**
    * Clear out all database items from table
    *
    */
    public function truncate($test = false)
    {
        if (empty($this->from) || !$this->from) return false;

        $sql = "TRUNCATE ".implode(',', $this->from);

        if ($test==true) return $sql;

        return $this->connection->query($sql);
    }


    //--------------------------------------------------------------------


    /**
    * Generates a FULL SQL query string.
    * Should not be called directly.
    *
    */
    protected function eqnumber($field, $eq)
    {
        if (empty($this->from) || !$this->from) return false;

        if ($eq == '') return false;

        $sql = 'SELECT '.strtoupper($eq)."(".$field.") AS eqnumber FROM ".implode(',', $this->from);
        $sql = $this->sqlWhere($sql);
        $sql = $this->sqlGroupBy($sql);
        $sql = $this->sqlOrderBy($sql);
        $sql = $this->sqlLimit($sql);

        $count = $this->connection->query($sql)->row();

        return $count->eqnumber ?? 0;
    }


    //--------------------------------------------------------------------


    /**
    * Generates a FULL SQL query string.
    * Should not be called directly.
    *
    * @return string
    */
    protected function buildSelect()
    {
        $sql = ( ! $this->distinct) ? 'SELECT ' : 'SELECT DISTINCT ';

        if (empty($this->select))
        {
            $sql .= '*';
        }
        else
        {
            $sql .= implode(',', $this->select);
        }

        // FROM logic
        if (! empty($this->from))
        {
            $sql .= " FROM " . implode(', ', $this->from);
        }

        // JOIN logic
        if (! empty($this->join))
        {
            foreach($this->join as $join)
            {
                $sql .= " ".$join['type']." JOIN ".$join['table'].' ON '.$join['condition'];
            }
        }

        $sql = $this->sqlWhere($sql);
        $sql = $this->sqlGroupBy($sql);
        $sql = $this->sqlHaving($sql);
        $sql = $this->sqlOrderBy($sql);
        $sql = $this->sqlLimit($sql);

        return $sql;
    }


    //--------------------------------------------------------------------


    /**
    * Generates the "SET" SQL query string.
    * Should not be called directly.
    *
    * @param string $sql
    * @return string
    */
    protected function sqlSet($sql)
    {
        if (!$this->set) return $sql;

        $sets = [];
        foreach($this->set as $field => $value)
        {
            if (!is_numeric($value)) $value = "'".$value."'";

            $sets[] = $field . ' = ' . $value;
        }

        return $sql . " SET " . implode(', ', $sets);
    }


    //--------------------------------------------------------------------


    /**
    * Generates the "WHERE" SQL query string.
    * Should not be called directly.
    *
    * @param string $sql
    * @return string
    */
    protected function sqlWhere($sql)
    {
        $start = false;
        foreach($this->where as $where)
        {
            if (!is_array($where['v']) && !is_numeric($where['v']) && !is_null($where['v'])) $where['v'] = "'".$where['v']."'";

            $sql .= (($start==false) ? ' WHERE' : ' AND');

            if ($where['o'] == 'IN' || $where['o'] == 'NOT IN')
            {
                $in = [];
                foreach($where['v'] as $val)
                {
                    $in[] = (!is_numeric($val) ? "'".$val."'" : $val);
                }

                $sql .= ' '.$where['f'].' '.$where['o'].' ('.implode(',',$in).')';
            }
            else
            {
                if (!is_null($where['v']))
                {
                    $sql .= ' '.$where['f'].' '.$where['o'].' '.$where['v'];
                }
                else
                {
                    $sql .= ' '.$where['f'];
                }
            }

            $start = true;
        }

        return $sql;
    }


    //--------------------------------------------------------------------


    /**
    * Generates the "HAVING" SQL query string.
    * Should not be called directly.
    *
    *
    * @param string $sql
    * @return string
    */
    protected function sqlHaving($sql)
    {
        $start = false;
        foreach($this->having as $having)
        {
            if (!is_array($having['v']) && !is_numeric($having['v']) && !is_null($having['v'])) $having['v'] = "'".$having['v']."'";

            $sql .= (($start==false) ? ' HAVING' : ' AND');

            if (!is_null($having['v']))
            {
                $sql .= ' '.$having['f'].' '.$having['o'].' '.$having['v'];
            }
            else
            {
                $sql .= ' '.$having['f'];
            }

            $start = true;
        }

        return $sql;
    }


    //--------------------------------------------------------------------


    /**
    * Generates the "ORDER BY" SQL query string.
    * Should not be called directly.
    *
    *
    * @param string $sql
    * @return string
    */
    protected function sqlOrderBy($sql)
    {
        if (!$this->order) return $sql;

        $orderBy = [];
        foreach($this->order as $order)
        {
            $orderBy[] = $order['field'] . ' ' . $order['dir'];
        }

        return $sql . " ORDER BY " . implode(', ', $orderBy);
    }


    //--------------------------------------------------------------------


    /**
    * Generates the "GROUP BY" SQL query string.
    * Should not be called directly.
    *
    *
    * @param string $sql
    * @return string
    */
    protected function sqlGroupBy($sql)
    {
        if (!$this->group) return $sql;

        return $sql . " GROUP BY " . implode(',', $this->group);
    }


    //--------------------------------------------------------------------


    /**
    * Generates the "LIMIT" SQL query string.
    * Should not be called directly.
    *
    *
    * @param string $sql
    * @return string
    */
    protected function sqlLimit($sql)
    {
        if (!$this->limit) return $sql;

        return $sql . " LIMIT " . ($this->offset ? $this->offset . ',' : '') . $this->limit;
    }


    //--------------------------------------------------------------------


    /**
    * Escape the string
    *
    * @param string $str
    * @return string
    */
    public function escape(string $str = '')
    {
        return $this->connection->escape($str);
    }


    //--------------------------------------------------------------------


    /**
    * Checks whether a SQL operator exist.
    *
    * @param string $str
    * @return bool
    */
    protected function hasOperator($str)
    {
        return (bool) preg_match('/(<|>|!|=|\sIS NULL|\sIS NOT NULL|\sEXISTS|\sBETWEEN|\sLIKE|\sIN\s*\(|\s)/i', trim($str));
    }


    // --------------------------------------------------------------------


    /**
    * Returns the SQL string operator
    *
    * @param string $str
    * @return string
    */
    protected function getOperator($str)
    {
        return preg_match('/' . implode('|', [
            // =, <=, >=, !=
            '\s*(?:<|>|!)?=\s*',
            // <, <>
            '\s*<>?\s*',
            // >
            '\s*>\s*',
            // BETWEEN value AND value
            '\s+BETWEEN\s+',
        ]) . '/i', $str, $match) ? $match[0] : false;
    }

}
