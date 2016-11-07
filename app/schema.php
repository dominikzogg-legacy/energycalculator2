<?php

use Doctrine\DBAL\Schema\Schema;

$schema = new Schema();

$users = $schema->createTable('users');
$users->addColumn('id', 'guid');
$users->addColumn('created_at', 'datetime');
$users->addColumn('updated_at', 'datetime', ['notnull' => false]);
$users->addColumn('email', 'string');
$users->addColumn('username', 'string');
$users->addColumn('password', 'string');
$users->addColumn('roles', 'text');
$users->setPrimaryKey(['id']);
$users->addUniqueIndex(['email']);
$users->addUniqueIndex(['username']);

$comestibles = $schema->createTable('comestibles');
$comestibles->addColumn('id', 'guid');
$comestibles->addColumn('created_at', 'datetime');
$comestibles->addColumn('updated_at', 'datetime', ['notnull' => false]);
$comestibles->addColumn('user_id', 'string');
$comestibles->addColumn('name', 'string');
$comestibles->addColumn('calorie', 'decimal', ['precision' => 10, 'scale' => 4]);
$comestibles->addColumn('protein', 'decimal', ['precision' => 10, 'scale' => 4]);
$comestibles->addColumn('carbohydrate', 'decimal', ['precision' => 10, 'scale' => 4]);
$comestibles->addColumn('fat', 'decimal', ['precision' => 10, 'scale' => 4]);
$comestibles->addColumn('default_value', 'decimal', ['precision' => 10, 'scale' => 4, 'notnull' => false]);
$comestibles->setPrimaryKey(['id']);
$comestibles->addUniqueIndex(['user_id', 'name']);
$comestibles->addForeignKeyConstraint($users, ['user_id'], ['id'], ['onDelete' => 'CASCADE']);

return $schema;
