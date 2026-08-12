-- =============== TASK 1 ===============

CREATE TABLE IF NOT EXISTS plannings (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    request_code VARCHAR(255) NOT NULL,
    candidate_token VARCHAR(255) NOT NULL,
    status VARCHAR(50) NOT NULL DEFAULT 'pending',
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    PRIMARY KEY (id),

    CONSTRAINT uq_plannings_request_code
        UNIQUE (request_code),

    CONSTRAINT chk_plannings_status
        CHECK (status IN ('pending', 'success', 'failed'))
);

CREATE TABLE IF NOT EXISTS planning_slots (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    planning_id BIGINT UNSIGNED NOT NULL,
    slot_order TINYINT UNSIGNED NOT NULL,
    slot_name VARCHAR(255) NOT NULL,
    original_quantity INT UNSIGNED NOT NULL,
    balanced_quantity INT UNSIGNED NULL,
    is_active BOOLEAN NOT NULL DEFAULT TRUE,
    created_at TIMESTAMP NULL,
    updated_at TIMESTAMP NULL,

    PRIMARY KEY (id),

    CONSTRAINT fk_planning_slots_planning
        FOREIGN KEY (planning_id)
        REFERENCES plannings(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_planning_slots_order
        UNIQUE (planning_id, slot_order),

    CONSTRAINT chk_planning_slots_quantity
        CHECK (
            original_quantity >= 0
            AND (
                balanced_quantity IS NULL
                OR balanced_quantity >= 0
            )
        )
);

-- =============== TASK 2 ===============
INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-001-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:00:00',
        '2026-08-12 09:00:00'
    );

SET @planning_id_1 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_1, 1, 'Senin',  4, 4, TRUE,  '2026-08-12 09:00:00', '2026-08-12 09:00:00'),
    (@planning_id_1, 2, 'Selasa', 5, 5, TRUE,  '2026-08-12 09:00:00', '2026-08-12 09:00:00'),
    (@planning_id_1, 3, 'Rabu',   1, 4, TRUE,  '2026-08-12 09:00:00', '2026-08-12 09:00:00'),
    (@planning_id_1, 4, 'Kamis',  7, 5, TRUE,  '2026-08-12 09:00:00', '2026-08-12 09:00:00'),
    (@planning_id_1, 5, 'Jumat',  6, 5, TRUE,  '2026-08-12 09:00:00', '2026-08-12 09:00:00'),
    (@planning_id_1, 6, 'Sabtu',  4, 4, TRUE,  '2026-08-12 09:00:00', '2026-08-12 09:00:00'),
    (@planning_id_1, 7, 'Minggu', 0, 0, FALSE, '2026-08-12 09:00:00', '2026-08-12 09:00:00');

INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-002-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:05:00',
        '2026-08-12 09:05:00'
    );

SET @planning_id_2 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_2, 1, 'Senin',   0, 0, FALSE, '2026-08-12 09:05:00', '2026-08-12 09:05:00'),
    (@planning_id_2, 2, 'Selasa',  0, 0, FALSE, '2026-08-12 09:05:00', '2026-08-12 09:05:00'),
    (@planning_id_2, 3, 'Rabu',    0, 0, FALSE, '2026-08-12 09:05:00', '2026-08-12 09:05:00'),
    (@planning_id_2, 4, 'Kamis',   0, 0, FALSE, '2026-08-12 09:05:00', '2026-08-12 09:05:00'),
    (@planning_id_2, 5, 'Jumat',   0, 0, FALSE, '2026-08-12 09:05:00', '2026-08-12 09:05:00'),
    (@planning_id_2, 6, 'Sabtu',   0, 0, FALSE, '2026-08-12 09:05:00', '2026-08-12 09:05:00'),
    (@planning_id_2, 7, 'Minggu',  0, 0, FALSE, '2026-08-12 09:05:00', '2026-08-12 09:05:00');

INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-003-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:10:00',
        '2026-08-12 09:10:00'
    );

SET @planning_id_3 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_3, 1, 'Senin',   0,  0, FALSE, '2026-08-12 09:10:00', '2026-08-12 09:10:00'),
    (@planning_id_3, 2, 'Selasa',  0,  0, FALSE, '2026-08-12 09:10:00', '2026-08-12 09:10:00'),
    (@planning_id_3, 3, 'Rabu',   15, 15, TRUE,  '2026-08-12 09:10:00', '2026-08-12 09:10:00'),
    (@planning_id_3, 4, 'Kamis',  0,  0, FALSE, '2026-08-12 09:10:00', '2026-08-12 09:10:00'),
    (@planning_id_3, 5, 'Jumat',  0,  0, FALSE, '2026-08-12 09:10:00', '2026-08-12 09:10:00'),
    (@planning_id_3, 6, 'Sabtu',  0,  0, FALSE, '2026-08-12 09:10:00', '2026-08-12 09:10:00'),
    (@planning_id_3, 7, 'Minggu', 0,  0, FALSE, '2026-08-12 09:10:00', '2026-08-12 09:10:00');

INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-004-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:15:00',
        '2026-08-12 09:15:00'
    );

SET @planning_id_4 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_4, 1, 'Senin',   5, 4, TRUE,  '2026-08-12 09:15:00', '2026-08-12 09:15:00'),
    (@planning_id_4, 2, 'Selasa',  5, 4, TRUE,  '2026-08-12 09:15:00', '2026-08-12 09:15:00'),
    (@planning_id_4, 3, 'Rabu',    1, 3, TRUE,  '2026-08-12 09:15:00', '2026-08-12 09:15:00'),
    (@planning_id_4, 4, 'Kamis',   0, 0, FALSE, '2026-08-12 09:15:00', '2026-08-12 09:15:00'),
    (@planning_id_4, 5, 'Jumat',   0, 0, FALSE, '2026-08-12 09:15:00', '2026-08-12 09:15:00'),
    (@planning_id_4, 6, 'Sabtu',   0, 0, FALSE, '2026-08-12 09:15:00', '2026-08-12 09:15:00'),
    (@planning_id_4, 7, 'Minggu',  0, 0, FALSE, '2026-08-12 09:15:00', '2026-08-12 09:15:00');

INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-005-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:20:00',
        '2026-08-12 09:20:00'
    );

SET @planning_id_5 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_5, 1, 'Senin',   1, 2, TRUE,  '2026-08-12 09:20:00', '2026-08-12 09:20:00'),
    (@planning_id_5, 2, 'Selasa',  2, 2, TRUE,  '2026-08-12 09:20:00', '2026-08-12 09:20:00'),
    (@planning_id_5, 3, 'Rabu',    4, 3, TRUE,  '2026-08-12 09:20:00', '2026-08-12 09:20:00'),
    (@planning_id_5, 4, 'Kamis',   0, 0, FALSE, '2026-08-12 09:20:00', '2026-08-12 09:20:00'),
    (@planning_id_5, 5, 'Jumat',   0, 0, FALSE, '2026-08-12 09:20:00', '2026-08-12 09:20:00'),
    (@planning_id_5, 6, 'Sabtu',   0, 0, FALSE, '2026-08-12 09:20:00', '2026-08-12 09:20:00'),
    (@planning_id_5, 7, 'Minggu',  0, 0, FALSE, '2026-08-12 09:20:00', '2026-08-12 09:20:00');

INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-006-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:25:00',
        '2026-08-12 09:25:00'
    );

SET @planning_id_6 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_6, 1, 'Senin',   1000000, 1166667, TRUE,  '2026-08-12 09:25:00', '2026-08-12 09:25:00'),
    (@planning_id_6, 2, 'Selasa',  500000,  1166666, TRUE,  '2026-08-12 09:25:00', '2026-08-12 09:25:00'),
    (@planning_id_6, 3, 'Rabu',    2000000, 1166667, TRUE,  '2026-08-12 09:25:00', '2026-08-12 09:25:00'),
    (@planning_id_6, 4, 'Kamis',   0,       0,       FALSE, '2026-08-12 09:25:00', '2026-08-12 09:25:00'),
    (@planning_id_6, 5, 'Jumat',   0,       0,       FALSE, '2026-08-12 09:25:00', '2026-08-12 09:25:00'),
    (@planning_id_6, 6, 'Sabtu',   0,       0,       FALSE, '2026-08-12 09:25:00', '2026-08-12 09:25:00'),
    (@planning_id_6, 7, 'Minggu',  0,       0,       FALSE, '2026-08-12 09:25:00', '2026-08-12 09:25:00');

INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-007-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:30:00',
        '2026-08-12 09:30:00'
    );

SET @planning_id_7 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_7, 1, 'Senin',   2, 3, TRUE,  '2026-08-12 09:30:00', '2026-08-12 09:30:00'),
    (@planning_id_7, 2, 'Selasa',  4, 3, TRUE,  '2026-08-12 09:30:00', '2026-08-12 09:30:00'),
    (@planning_id_7, 3, 'Rabu',    2, 3, TRUE,  '2026-08-12 09:30:00', '2026-08-12 09:30:00'),
    (@planning_id_7, 4, 'Kamis',   4, 3, TRUE,  '2026-08-12 09:30:00', '2026-08-12 09:30:00'),
    (@planning_id_7, 5, 'Jumat',   0, 0, FALSE, '2026-08-12 09:30:00', '2026-08-12 09:30:00'),
    (@planning_id_7, 6, 'Sabtu',   0, 0, FALSE, '2026-08-12 09:30:00', '2026-08-12 09:30:00'),
    (@planning_id_7, 7, 'Minggu',  0, 0, FALSE, '2026-08-12 09:30:00', '2026-08-12 09:30:00');

INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-008-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:35:00',
        '2026-08-12 09:35:00'
    );

SET @planning_id_8 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_8, 1, 'Senin',   10, 6, TRUE,   '2026-08-12 09:35:00', '2026-08-12 09:35:00'),
    (@planning_id_8, 2, 'Selasa',   0, 0, FALSE,  '2026-08-12 09:35:00', '2026-08-12 09:35:00'),
    (@planning_id_8, 3, 'Rabu',     1, 5, TRUE,   '2026-08-12 09:35:00', '2026-08-12 09:35:00'),
    (@planning_id_8, 4, 'Kamis',    0, 0, FALSE,  '2026-08-12 09:35:00', '2026-08-12 09:35:00'),
    (@planning_id_8, 5, 'Jumat',    5, 5, TRUE,   '2026-08-12 09:35:00', '2026-08-12 09:35:00'),
    (@planning_id_8, 6, 'Sabtu',    0, 0, FALSE,  '2026-08-12 09:35:00', '2026-08-12 09:35:00'),
    (@planning_id_8, 7, 'Minggu',   0, 0, FALSE,  '2026-08-12 09:35:00', '2026-08-12 09:35:00');

INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-009-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:40:00',
        '2026-08-12 09:40:00'
    );

SET @planning_id_9 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_9, 1, 'Senin',   3, 4, TRUE,   '2026-08-12 09:40:00', '2026-08-12 09:40:00'),
    (@planning_id_9, 2, 'Selasa',  8, 4, TRUE,   '2026-08-12 09:40:00', '2026-08-12 09:40:00'),
    (@planning_id_9, 3, 'Rabu',    1, 4, TRUE,   '2026-08-12 09:40:00', '2026-08-12 09:40:00'),
    (@planning_id_9, 4, 'Kamis',   4, 4, TRUE,   '2026-08-12 09:40:00', '2026-08-12 09:40:00'),
    (@planning_id_9, 5, 'Jumat',   0, 0, FALSE,  '2026-08-12 09:40:00', '2026-08-12 09:40:00'),
    (@planning_id_9, 6, 'Sabtu',   0, 0, FALSE,  '2026-08-12 09:40:00', '2026-08-12 09:40:00'),
    (@planning_id_9, 7, 'Minggu',  0, 0, FALSE,  '2026-08-12 09:40:00', '2026-08-12 09:40:00');

INSERT INTO plannings
    (request_code, candidate_token, status, created_at, updated_at)
VALUES
    (
        'REQ-010-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'success',
        '2026-08-12 09:45:00',
        '2026-08-12 09:45:00'
    );

SET @planning_id_10 = LAST_INSERT_ID();

INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@planning_id_10, 1, 'Senin',   6, 4, TRUE,  '2026-08-12 09:45:00', '2026-08-12 09:45:00'),
    (@planning_id_10, 2, 'Selasa',  2, 4, TRUE,  '2026-08-12 09:45:00', '2026-08-12 09:45:00'),
    (@planning_id_10, 3, 'Rabu',    7, 4, TRUE,  '2026-08-12 09:45:00', '2026-08-12 09:45:00'),
    (@planning_id_10, 4, 'Kamis',   1, 4, TRUE,  '2026-08-12 09:45:00', '2026-08-12 09:45:00'),
    (@planning_id_10, 5, 'Jumat',   4, 4, TRUE,  '2026-08-12 09:45:00', '2026-08-12 09:45:00'),
    (@planning_id_10, 6, 'Sabtu',   0, 0, FALSE, '2026-08-12 09:45:00', '2026-08-12 09:45:00'),
    (@planning_id_10, 7, 'Minggu',  0, 0, FALSE, '2026-08-12 09:45:00', '2026-08-12 09:45:00');


-- =============== TASK 3 ===============

SELECT
    p.id AS PlanningId,
    COALESCE(SUM(ps.original_quantity), 0) AS OriginalTotal,
    COALESCE(SUM(ps.balanced_quantity), 0) AS BalancedTotal,
    CASE
        WHEN COALESCE(SUM(ps.original_quantity), 0) = COALESCE(SUM(ps.balanced_quantity), 0)
        THEN TRUE
        ELSE FALSE
    END AS IsTotalValid
FROM plannings p
LEFT JOIN planning_slots ps
    ON ps.planning_id = p.id
GROUP BY p.id
ORDER BY p.id;


-- =============== TASK 4 ===============

SELECT
    p.request_code AS RequestCode,
    p.created_at AS CreatedAt,
    COUNT(
        CASE
            WHEN ps.is_active = TRUE THEN 1
        END
    ) AS ActiveSlotCount,
    COALESCE(SUM(ps.original_quantity), 0) AS OriginalTotal,
    COALESCE(SUM(ps.balanced_quantity), 0) AS BalancedTotal,
    p.status AS Status
FROM plannings p
LEFT JOIN planning_slots ps
    ON ps.planning_id = p.id
GROUP BY
    p.id,
    p.request_code,
    p.created_at,
    p.status
ORDER BY p.created_at DESC;

-- =============== TASK 5 ===============

SELECT
    p.id AS PlanningId,
    p.request_code AS RequestCode,
    ps.id AS PlanningSlotId,
    ps.slot_order AS SlotOrder,
    ps.balanced_quantity AS BalancedQuantity,
    'INACTIVE_SLOT_HAS_BALANCED_QUANTITY' AS AnomalyType
FROM plannings p
JOIN planning_slots ps
    ON ps.planning_id = p.id
WHERE ps.is_active = FALSE
  AND COALESCE(ps.balanced_quantity, 0) > 0;


SELECT
    p.id AS PlanningId,
    p.request_code AS RequestCode,
    COALESCE(SUM(ps.original_quantity), 0) AS OriginalTotal,
    COALESCE(SUM(ps.balanced_quantity), 0) AS BalancedTotal,
    'TOTAL_MISMATCH' AS AnomalyType
FROM plannings p
JOIN planning_slots ps
    ON ps.planning_id = p.id
GROUP BY
    p.id,
    p.request_code
HAVING
    COALESCE(SUM(ps.original_quantity), 0)
    <>
    COALESCE(SUM(ps.balanced_quantity), 0);

SELECT
    p.id AS PlanningId,
    p.request_code AS RequestCode,
    COUNT(ps.id) AS SlotCount,
    'INCOMPLETE_SLOT_DETAILS' AS AnomalyType
FROM plannings p
LEFT JOIN planning_slots ps
    ON ps.planning_id = p.id
GROUP BY
    p.id,
    p.request_code
HAVING COUNT(ps.id) <> 7;

SELECT
    request_code AS RequestCode,
    COUNT(*) AS DuplicateCount,
    'DUPLICATE_REQUEST_CODE' AS AnomalyType
FROM plannings
GROUP BY request_code
HAVING COUNT(*) > 1;


-- =============== TASK 6 ===============

SELECT
    p.id AS PlanningId,
    p.request_code AS RequestCode,
    ps.slot_order AS SlotOrder,
    ps.slot_name AS SlotName,
    ps.original_quantity AS OriginalQuantity,
    ps.balanced_quantity AS BalancedQuantity,
    ABS(
        COALESCE(ps.balanced_quantity, 0)
        - ps.original_quantity
    ) AS AbsoluteAdjustment
FROM plannings p
JOIN planning_slots ps
    ON ps.planning_id = p.id
ORDER BY
    AbsoluteAdjustment DESC,
    ps.slot_order ASC
LIMIT 3;


-- =============== TASK 7 ===============

START TRANSACTION;

INSERT INTO plannings
    (
        request_code,
        candidate_token,
        status,
        created_at,
        updated_at
    )
VALUES
    (
        'REQ-ATOMIC-VEH-CANDIDATE_CODE',
        'VEH-CANDIDATE_CODE',
        'pending',
        NOW(),
        NOW()
    );

SET @atomic_planning_id = LAST_INSERT_ID();


INSERT INTO planning_slots
    (
        planning_id,
        slot_order,
        slot_name,
        original_quantity,
        balanced_quantity,
        is_active,
        created_at,
        updated_at
    )
VALUES
    (@atomic_planning_id, 1, 'Senin',   4, 4, TRUE,  NOW(), NOW()),
    (@atomic_planning_id, 2, 'Selasa', 5, 5, TRUE,  NOW(), NOW()),
    (@atomic_planning_id, 3, 'Rabu',   1, 4, TRUE,  NOW(), NOW()),
    (@atomic_planning_id, 4, 'Kamis',  7, 5, TRUE,  NOW(), NOW()),
    (@atomic_planning_id, 5, 'Jumat',  6, 5, TRUE,  NOW(), NOW()),
    (@atomic_planning_id, 6, 'Sabtu',  4, 4, TRUE,  NOW(), NOW()),
    (@atomic_planning_id, 7, 'Minggu', 0, 0, FALSE, NOW(), NOW());

COMMIT;

-- =============== TASK 8 ===============

CREATE TABLE IF NOT EXISTS rebalance_runs (
    id BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
    planning_id BIGINT UNSIGNED NOT NULL,
    version INT UNSIGNED NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    CONSTRAINT fk_rebalance_runs_planning
        FOREIGN KEY (planning_id)
        REFERENCES plannings(id)
        ON DELETE CASCADE,

    CONSTRAINT uq_rebalance_runs_version
        UNIQUE (planning_id, version)
);


INSERT INTO rebalance_runs
    (planning_id, version, created_at)
VALUES
    (@planning_id_1, 1, '2026-08-12 09:00:00'),
    (@planning_id_1, 2, '2026-08-12 10:00:00'),
    (@planning_id_2, 1, '2026-08-12 09:05:00'),
    (@planning_id_3, 1, '2026-08-12 09:10:00');

WITH ranked_runs AS (
    SELECT
        rr.id,
        rr.planning_id,
        rr.version,
        rr.created_at,
        ROW_NUMBER() OVER (
            PARTITION BY rr.planning_id
            ORDER BY rr.version DESC, rr.id DESC
        ) AS row_number
    FROM rebalance_runs rr
)
SELECT
    id AS RebalanceRunId,
    planning_id AS PlanningId,
    version AS Version,
    created_at AS CreatedAt
FROM ranked_runs
WHERE row_number = 1
ORDER BY planning_id;