<?php

use App\Permission;
use Illuminate\Database\Seeder;




class PermissionsTableSeeder extends Seeder
{
    public function run()
    {
        $createInvoice = new Permission();
        $createInvoice ->name = 'create_invoice';
        $createInvoice ->display_name = 'Create Invoice'; // optional
        $createInvoice  -> description = 'create new invoice'; // optional
        $createInvoice ->save();
        
        $editInvoice = new Permission();
        $editInvoice ->name = 'edit_invoice';
        $editInvoice ->display_name = 'Edit Invoice'; // optional
        $editInvoice  -> description = 'edit existing invoice'; // optional
        $editInvoice ->save();
        
        $deleteInvoice = new Permission();
        $deleteInvoice ->name = 'delete_invoice';
        $deleteInvoice ->display_name = 'Delete Invoice'; // optional
        $deleteInvoice  -> description = 'delete existing invoice'; // optional
        $deleteInvoice ->save();
    }
}
