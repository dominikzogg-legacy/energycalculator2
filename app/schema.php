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

return $schema;
