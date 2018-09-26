<?php
namespace agilman\a2\model;

/**
 * Class TransactionCollectionModel
 *
 * @package agilman/a2
 * @author Sven Gerhards
 */

class TransactionCollectionModel extends Model
{
    private $transactionsIds;

    private $N;

    public function __construct()
    {
        parent::__construct();
        if (!$result = $this->db->query("SELECT `id` FROM `transactions`;")) {
            // throw new ...
        }
        $this->transactionsIds = array_column($result->fetch_all(), 0);
        $this->N = $result->num_rows;
    }

    /**
     * Get transaction collection
     *
     * @return \Generator|TransactionModel[] Transactions
     */
    public function getTransactions()
    {
        foreach ($this->transactionsIds as $id) {
            // Use a generator to save on memory/resources
            // load accounts from DB one at a time only when required
            yield (new TransactionModel())->load($id);
        }
    }
}
