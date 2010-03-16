#! /usr/bin/env php
<?php
    require_once('utilities.php');
    require_once(IA_ROOT_DIR . "common/db/db.php");
    ini_set("memory_limit", "1024M");
    db_connect();

    $query = "CREATE TABLE `ia_score_user_round` (
                user_id int,
                round_id varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci,
                score decimal(11, 4),
                CONSTRAINT user_round UNIQUE (user_id, round_id)
                )";
    db_query($query);

    $query = "CREATE TABLE `ia_score_user_round_task` (
                user_id int,
                round_id varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci,
                task_id varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci,
                score decimal(11, 4),
                CONSTRAINT user_round_task UNIQUE (user_id, round_id, task_id)
                )";
    db_query($query);

    $query = "CREATE TABLE `ia_rating` (
                user_id int,
                round_id varchar(64) CHARACTER SET latin1 COLLATE latin1_general_ci,
                deviation decimal(11, 4),
                rating decimal(11, 4),
                CONSTRAINT user_round UNIQUE (user_id, round_id)
                )";
    db_query($query);

    $query = "SELECT * FROM `ia_score`";
    $result = db_fetch_all($query);

    foreach ($result as $score) {
        if ($score['name'] == 'deviation' || $score['name'] == 'rating') {
            $where = '`user_id` = '. db_quote($score['user_id']) ." && `round_id` = " . db_quote($score['round_id']);

            $query = "SELECT * FROM `ia_rating` WHERE ".$where;
            $result = db_fetch_all($query);
            if (!$result) {
                 $query = "INSERT INTO `ia_rating`(`user_id`, `round_id`, `deviation`, `rating`)
                            VALUES (".db_quote($score['user_id']).", ".db_quote($score['round_id']).", '0', '0')";
                 db_query($query);
            }
 
            $query = "UPDATE `ia_rating` SET `".$score['name']."` = ".db_quote($score['score'])." WHERE ".$where;
            db_query($query);
        } else if ($score['name'] == 'score') {
            $query = "INSERT INTO `ia_score_user_round_task` (`user_id`, `round_id`, `task_id`, `score`)
                        VALUES (".implode(',',
                            array(
                                db_quote($score['user_id']),
                                db_quote($score['round_id']), 
                                db_quote($score['task_id']),
                                db_quote($score['score']))
                            ).")";
            db_query($query);

            $query = "SELECT `score` FROM `ia_score_user_round` WHERE
                        `user_id` = ".db_quote($score['user_id'])." &&
                        `round_id` = ".db_quote($score['round_id']);
            $result = db_fetch_all($query);

            if (!$result) {
                $query = "INSERT INTO `ia_score_user_round` (`user_id`, `round_id`, `score`)
                        VALUES (".implode(',',
                            array(
                                db_quote($score['user_id']),
                                db_quote($score['round_id']),
                                db_quote($score['score'])
                                )
                            ).")";
            } else {
                $query = "UPDATE `ia_score_user_round` SET `score` = `score` + ".$score['score']." WHERE
                         `user_id` = ".db_quote($score['user_id'])." &&
                        `round_id` = ".db_quote($score['round_id']);
            }
            db_query($query);
        } else echo implode(' | ', $score)." dumped\n";
    }

    $query = "SELECT * FROM `ia_round`";
    $rounds = db_fetch_all($query);

//    $query = "DROP TABLE `ia_score`";
//    db_query($query)
?>
