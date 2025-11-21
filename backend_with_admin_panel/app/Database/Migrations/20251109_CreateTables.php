<?php namespace App\Database\Migrations;

use CodeIgniter\Database\Migration;

class CreateTables extends Migration
{
    public function up()
    {
        // users
        $this->forge->addField([
            'id'          => ['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'username'    => ['type'=>'VARCHAR','constraint'=>100,'null'=>true],
            'email'       => ['type'=>'VARCHAR','constraint'=>150],
            'password'    => ['type'=>'VARCHAR','constraint'=>255],
            'role'        => ['type'=>'ENUM','constraint'=>"'admin','customer'",'default'=>'customer'],
            'created_at'  => ['type'=>'TIMESTAMP','default'=>'CURRENT_TIMESTAMP']
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addUniqueKey('email');
        $this->forge->createTable('users');

        // categories
        $this->forge->addField([
            'id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'name'=>['type'=>'VARCHAR','constraint'=>150],
            'description'=>['type'=>'TEXT','null'=>true],
            'created_at'=>['type'=>'TIMESTAMP','default'=>'CURRENT_TIMESTAMP']
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('categories');

        // products
        $this->forge->addField([
            'id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'name'=>['type'=>'VARCHAR','constraint'=>255],
            'description'=>['type'=>'TEXT','null'=>true],
            'price'=>['type'=>'DECIMAL','constraint'=>'10,2','default'=>0.00],
            'category_id'=>['type'=>'INT','null'=>true],
            'stock'=>['type'=>'INT','default'=>0],
            'image_url'=>['type'=>'VARCHAR','constraint'=>512,'null'=>true],
            'created_at'=>['type'=>'TIMESTAMP','default'=>'CURRENT_TIMESTAMP']
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('category_id','categories','id','SET NULL','CASCADE');
        $this->forge->createTable('products');

        // orders
        $this->forge->addField([
            'id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'user_id'=>['type'=>'INT'],
            'total_price'=>['type'=>'DECIMAL','constraint'=>'12,2'],
            'status'=>['type'=>'ENUM','constraint'=>"'pending','processing','shipped','delivered','cancelled'","default"=>"pending"],
            'created_at'=>['type'=>'TIMESTAMP','default'=>'CURRENT_TIMESTAMP']
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('user_id','users','id','CASCADE','CASCADE');
        $this->forge->createTable('orders');

        // order_items
        $this->forge->addField([
            'id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'order_id'=>['type'=>'INT'],
            'product_id'=>['type'=>'INT'],
            'quantity'=>['type'=>'INT'],
            'price'=>['type'=>'DECIMAL','constraint'=>'10,2']
        ]);
        $this->forge->addKey('id', true);
        $this->forge->addForeignKey('order_id','orders','id','CASCADE','CASCADE');
        $this->forge->addForeignKey('product_id','products','id','CASCADE','CASCADE');
        $this->forge->createTable('order_items');

        // settings
        $this->forge->addField([
            'id'=>['type'=>'INT','constraint'=>11,'unsigned'=>true,'auto_increment'=>true],
            'key'=>['type'=>'VARCHAR','constraint'=>150],
            'value'=>['type'=>'TEXT','null'=>true],
            'created_at'=>['type'=>'TIMESTAMP','default'=>'CURRENT_TIMESTAMP']
        ]);
        $this->forge->addKey('id', true);
        $this->forge->createTable('settings');
    }

    public function down()
    {
        $this->forge->dropTable('order_items', true);
        $this->forge->dropTable('orders', true);
        $this->forge->dropTable('products', true);
        $this->forge->dropTable('categories', true);
        $this->forge->dropTable('users', true);
        $this->forge->dropTable('settings', true);
    }
}
