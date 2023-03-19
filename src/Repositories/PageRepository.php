<?php
declare(strict_types=1);

namespace Chlp\Telepage\Repositories;

use Medoo\Medoo;

class PageRepository {
    /**
     * @param Medoo $db
     */
    public function __construct(
        private Medoo $db,
    ) {
    }

    /**
     * @return array|null
     */
    public function test(): ?array {
        return $this->db->select('emp0', [
            'badgeNo',
            'qt_customerId'
        ], [
            'id' => 1
        ]);
    }
}