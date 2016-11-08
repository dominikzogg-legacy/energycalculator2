<?php

use Doctrine\DBAL\Schema\Schema;

$schema = new Schema();

$users = $schema->createTable('users');
$users->addColumn('id', 'guid');
$users->addColumn('createdAt', 'datetime');
$users->addColumn('updatedAt', 'datetime', ['notnull' => false]);
$users->addColumn('email', 'string');
$users->addColumn('username', 'string');
$users->addColumn('password', 'string');
$users->addColumn('roles', 'text');
$users->setPrimaryKey(['id']);
$users->addUniqueIndex(['email']);
$users->addUniqueIndex(['username']);

$comestibles = $schema->createTable('comestibles');
$comestibles->addColumn('id', 'guid');
$comestibles->addColumn('createdAt', 'datetime');
$comestibles->addColumn('updatedAt', 'datetime', ['notnull' => false]);
$comestibles->addColumn('userId', 'string');
$comestibles->addColumn('name', 'string');
$comestibles->addColumn('calorie', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestibles->addColumn('protein', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestibles->addColumn('carbohydrate', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestibles->addColumn('fat', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestibles->addColumn('defaultValue', 'decimal', ['precision' => 7, 'scale' => 1, 'notnull' => false]);
$comestibles->setPrimaryKey(['id']);
$comestibles->addUniqueIndex(['userId', 'name']);
$comestibles->addForeignKeyConstraint($users, ['userId'], ['id'], ['onDelete' => 'CASCADE']);

$day = $schema->createTable('day');
$day->addColumn('id', 'guid');
$day->addColumn('createdAt', 'datetime');
$day->addColumn('updatedAt', 'datetime', ['notnull' => false]);
$day->addColumn('userId', 'string');
$day->addColumn('date', 'date');
$day->addColumn('weight', 'decimal', ['precision' => 7, 'scale' => 1]);
$day->setPrimaryKey(['id']);
$day->addUniqueIndex(['userId', 'date']);
$day->addForeignKeyConstraint($users, ['userId'], ['id'], ['onDelete' => 'CASCADE']);

$comestibleWithinDay = $schema->createTable('comestible_within_day');
$comestibleWithinDay->addColumn('id', 'guid');
$comestibleWithinDay->addColumn('createdAt', 'datetime');
$comestibleWithinDay->addColumn('updatedAt', 'datetime', ['notnull' => false]);
$comestibleWithinDay->addColumn('dayId', 'string');
$comestibleWithinDay->addColumn('comestibleId', 'string');
$comestibleWithinDay->addColumn('amount', 'decimal', ['precision' => 7, 'scale' => 1]);
$comestibleWithinDay->setPrimaryKey(['id']);
$comestibleWithinDay->addForeignKeyConstraint($day, ['dayId'], ['id'], ['onDelete' => 'CASCADE']);
$comestibleWithinDay->addForeignKeyConstraint($comestibles, ['comestibleId'], ['id'], ['onDelete' => 'CASCADE']);

return $schema;
