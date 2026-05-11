<?php return array (
  'Illuminate\\Foundation\\Support\\Providers\\EventServiceProvider' => 
  array (
    'App\\Events\\TransactionCompleted' => 
    array (
      0 => 'App\\Listeners\\DeductInventoryForTransaction@handle',
      1 => 'App\\Listeners\\SendLowStockNotification@handle',
    ),
    'App\\Events\\InventoryMoved' => 
    array (
      0 => 'App\\Listeners\\LogInventoryMovement@handle',
      1 => 'App\\Listeners\\ProcessAccountingForInventory@handle',
    ),
    'App\\Events\\TransactionCreated' => 
    array (
      0 => 'App\\Listeners\\ProcessInventoryReduction@handle',
      1 => 'App\\Listeners\\RecordAccountingJournal@handle',
      2 => 'App\\Listeners\\UpdateCustomerLoyalty@handle',
    ),
  ),
);