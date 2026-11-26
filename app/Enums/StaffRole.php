<?php

namespace App\Enums;

enum StaffRole: string
{
    case Administrator = 'administrator';
    case CashierBar = 'cashier_bar';
    case Barista = 'barista';
    case Waiter = 'waiter';
    case Cook = 'cook';
    case BakerPastry = 'baker_pastry';
    case KitchenAssistant = 'kitchen_assistant';
    case Cleaning = 'cleaning';
    case Delivery = 'delivery';

    public function label(): string
    {
        return match ($this) {
            self::Administrator => 'Administrador',
            self::CashierBar => 'Cajero / Barra',
            self::Barista => 'Barista',
            self::Waiter => 'Mozo',
            self::Cook => 'Cocinero',
            self::BakerPastry => 'Panadero / Pastelero',
            self::KitchenAssistant => 'Ayudante de cocina',
            self::Cleaning => 'Limpieza / Lavaplatos',
            self::Delivery => 'Repartidor',
        };
    }

    public static function options(): array
    {
        $options = [];

        foreach (self::cases() as $role) {
            $options[$role->value] = $role->label();
        }

        return $options;
    }

    public function allows(string $permission): bool
    {
        $permissions = match ($this) {
            self::Administrator => ['*'],
            self::CashierBar => [
                'dashboard.view',
                'dashboard.financials',
                'customers.view',
                'customers.create',
                'customers.update',
                'customers.export',
                'tables.view',
                'orders.view',
                'pos.access',
                'cash.manage',
                'comandas.view',
                'comandas.create',
                'comandas.update',
            ],
            self::Waiter => [
                'dashboard.view',
                'customers.view',
                'customers.create',
                'tables.view',
                'tables.update',
                'comandas.view',
                'comandas.create',
                'comandas.update',
            ],
            self::Barista => [
                'dashboard.view',
                'kds.view',
                'kds.update',
            ],
            self::Cook,
            self::KitchenAssistant => [
                'dashboard.view',
                'kds.view',
                'kds.update',
            ],
            self::Cleaning => [
                'dashboard.view',
                'tables.view',
                'tables.update',
            ],
            self::BakerPastry,
            self::Delivery => [
                'dashboard.view',
            ],
        };

        return in_array('*', $permissions, true) || in_array($permission, $permissions, true);
    }
}
