<?php namespace Base\Database\Builder;


/**
* Class Builder
*
*/
class Query extends Database
{

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
    protected $groupBy = [];


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
    * ORDER BY
    *
    * @var array
    */
    public $orderBy = [];


    /**
    * SET
    *
    * @var array
    */
    protected $set = [];



    /**
    * SELECT
    *
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


    /**
    * FROM
    *
    * @return $this
    */
    public function table($from)
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


    /**
    * WHERE
    *
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
            if ($v !== null)
            {
                $op = $this->getOperator($k);
                $k = trim(str_replace($op, '', $k));

                if (!empty($op))
                {
                    $o = $op;
                }
            }
            elseif ( ! $this->hasOperator($k))
            {
                // assign this "IS NULL" (missing operator/value)
                $k .= ' IS NULL';
            }
            elseif (preg_match('/\s*(!?=|<>|IS(?:\s+NOT)?)\s*$/i', $k, $match, PREG_OFFSET_CAPTURE))
            {
                $k = substr($k, 0, $match[0][1]) . ($match[1][0] === '=' ? ' IS NULL' : ' IS NOT NULL');
            }

            $this->where[] = [
                'f' => $k,
                'o' => $o,
                'v' => $this->escape($v)
            ];

        }

        return $this;
    }


    /**
    * WHERE IN (...)
    *
    * ->in('field', [1,2,3])
    *
    *
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
            $vals[] = $this->escape($v);
        }

        $this->where[] = [
            'f' => $key,
            'o' => 'IN',
            'v' => $vals
        ];

        return $this;
    }


    /**
    * WHERE NOT IN (...)
    *
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
            $vals[] = $this->escape($v);
        }

        $this->where[] = [
            'f' => $key,
            'o' => 'NOT IN',
            'v' => $vals
        ];

        return $this;
    }


    /**
    * ORDER BY
    *
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

                $this->orderBy[] = [
                    'field' => ($val) ? ltrim(substr($field, 0, $match[0][1])) : $field,
                    'dir' => ($val) ? $match[1][0] : $direction
                ];
            }
        }

        return $this;
    }


    /**
    * GROUP BY
    *
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
                $this->groupBy[] = $val;
            }
        }

        return $this;
    }



    /**
    * LIMIT
    *
    * @return $this
    */
    public function limit(int $value = null, int $offset = 0)
    {
        if ( ! is_null($value))
        {
            $this->limit = $value;
        }

        if ( ! empty($offset))
        {
            $this->offset = $offset;
        }

        return $this;
    }


    /**
    * OFFSET
    *
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


    /**
    * SET
    *
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


    /**
    * Run the query and return the results
    *
    */
    public function get($test = false)
    {
        $sql = $this->buildSelect();

        $this->resetAll();

        if ($test==true) return $sql;

        return $this->db->query($sql);
    }


    /**
    * Run the query and return the results
    *
    */
    public function row($test = false)
    {
        if ($test==true) return $this->get(true);

        return $this->get($test)->row();
    }


    /**
    * Run the query and return the results
    *
    */
    public function results($test = false)
    {
        if ($test==true) return $this->get(true);

        return $this->get($test)->results();
    }


    /**
    * Run the query and return the results
    *
    */
    public function first($test = false)
    {
        if ($test==true) return $this->get(true);

        return $this->get($test)->results()[0] ?? false;
    }


    /**
    * Escape the string
    *
    */
    public function escape(string $str = '')
    {
        return $this->db->real_escape_string($str);
    }


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

        $this->resetAll();

        if ($test==true) return $sql;

        return $this->db->query($sql);
    }


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

        $this->resetAll();

        if ($test==true) return $sql;

        return $this->db->query($sql);
    }


    /**
    * Run the query and return the results
    *
    */
    public function increment($field, $number, $test = false)
    {
        return $this->eqmath($field, $number, '+', $test);
    }


    /**
    * Run the query and return the results
    *
    */
    public function decrement($field, $number, $test = false)
    {
        return $this->eqmath($field, $number, '-', $test);
    }


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

        $this->resetAll();

        if ($test==true) return $sql;

        return $this->db->query($sql);
    }


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

        $this->resetAll();

        if ($test==true) return $sql;

        return $this->db->query($sql);
    }


    /**
	 * avg
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

        $this->resetAll();

        $count = $this->db->query($sql)->row();

		return $count->eqnumber ?? 0;
	}


    /**
	 * count
	 *
	 */
	public function count()
	{
		return $this->eqnumber("*", 'COUNT');
	}


    /**
	 * avg
	 *
	 */
	public function avg($field)
	{
		return $this->eqnumber($field, 'AVG');
	}


    /**
	 * max
	 *
	 */
	public function max($field)
	{
        return $this->eqnumber($field, 'MAX');
	}


    /**
	 * min
	 *
	 */
	public function min($field)
	{
        return $this->eqnumber($field, 'MIN');
	}


    /**
	 * sum
	 *
	 */
	public function sum($field)
	{
        return $this->eqnumber($field, 'SUM');
	}


    /**
    * Clear out all database items from table
    *
    */
    public function truncate($test = false)
    {
        if (empty($this->from) || !$this->from) return false;

        $sql = "TRUNCATE ".implode(',', $this->from);

        $this->resetAll();

        if ($test==true) return $sql;

        return $this->db->query($sql);
    }


    //--------------------------------------------------------------------


    /**
    * Build the SELECT statement
    *
    * Generates a query string based on which functions were used.
    * Should not be called directly.
    *
    * @param    bool $select_override
    *
    * @return    string
    */
    protected function buildSelect()
    {
        $sql = 'SELECT ';

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

        $sql = $this->sqlWhere($sql);
        $sql = $this->sqlGroupBy($sql);
        $sql = $this->sqlOrderBy($sql);
        $sql = $this->sqlLimit($sql);

        return $sql;
    }


    //--------------------------------------------------------------------


    /**
    * BUILD SQL "SET"
    *
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
    * BUILD SQL "WHERE"
    *
    *
    * @param string $sql
    * @return string
    */
    protected function sqlWhere($sql)
    {
        $start = false;
        foreach($this->where as $where)
        {
            if (!is_array($where['v']) && !is_numeric($where['v'])) $where['v'] = "'".$where['v']."'";

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
                $sql .= ' '.$where['f'].' '.$where['o'].' '.$where['v'];
            }

            $start = true;
        }

        return $sql;
    }


    //--------------------------------------------------------------------


    /**
    * BUILD SQL "ORDER BY"
    *
    *
    * @param string $sql
    * @return string
    */
    protected function sqlOrderBy($sql)
    {
        if (!$this->orderBy) return $sql;

        $orderBy = [];
        foreach($this->orderBy as $order)
        {
            $orderBy[] = $order['field'] . ' ' . $order['dir'];
        }

        return $sql . " ORDER BY " . implode(', ', $orderBy);
    }


    //--------------------------------------------------------------------


    /**
    * BUILD SQL "GROUP BY"
    *
    *
    * @param string $sql
    * @return string
    */
    protected function sqlGroupBy($sql)
    {
        if (!$this->groupBy) return $sql;

        return $sql . " GROUP BY " . implode(',', $this->groupBy);
    }


    //--------------------------------------------------------------------


    /**
    * BUILD SQL "LIMIT" (and OFFSET)
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
    * Resets the class variable values to their defaults
    *
    * @param array $items
    */
    protected function reset($items)
    {
        foreach ($items as $item => $default_value)
        {
            $this->$item = $default_value;
        }
    }


    //--------------------------------------------------------------------


    /**
    * Reset all the SQL statement variables
    *
    */
    protected function resetAll()
    {
        $this->reset([
            'select'   => [],
            'where'	   => [],
            'from'     => [],
            'having'   => [],
            'groupBy'  => [],
            'orderBy'  => [],
            'set'      => [],
            'limit'    => false,
            'offset'   => false
        ]);
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
