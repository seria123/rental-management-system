<?php

namespace Database\Seeders;

use App\Models\SystemSetting;
use Illuminate\Database\Seeder;

class SystemSettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            // General Settings
            [
                'key' => 'system_name',
                'group' => 'general',
                'value' => 'Rental Management System',
                'input_type' => 'text',
                'description' => 'The name of the application displayed in headers and emails.',
            ],
            [
                'key' => 'company_name',
                'group' => 'general',
                'value' => 'Ndiro Estates',
                'input_type' => 'text',
                'description' => 'The company or property management business name.',
            ],
            [
                'key' => 'company_email',
                'group' => 'general',
                'value' => '',
                'input_type' => 'text',
                'description' => 'Official company email address.',
            ],
            [
                'key' => 'company_phone',
                'group' => 'general',
                'value' => '',
                'input_type' => 'text',
                'description' => 'Official company phone number.',
            ],
            [
                'key' => 'company_address',
                'group' => 'general',
                'value' => '',
                'input_type' => 'text',
                'description' => 'Physical company address.',
            ],
            [
                'key' => 'company_website',
                'group' => 'general',
                'value' => '',
                'input_type' => 'text',
                'description' => 'Company website URL.',
            ],
            [
                'key' => 'currency',
                'group' => 'general',
                'value' => 'KES',
                'input_type' => 'text',
                'description' => 'Default currency for all financial transactions.',
            ],
            [
                'key' => 'timezone',
                'group' => 'general',
                'value' => 'Africa/Nairobi',
                'input_type' => 'text',
                'description' => 'System timezone for dates and times.',
            ],
            [
                'key' => 'date_format',
                'group' => 'general',
                'value' => 'd M Y',
                'input_type' => 'text',
                'description' => 'Default date display format.',
            ],

            // Property Settings
            [
                'key' => 'unit_numbering_format',
                'group' => 'property',
                'value' => '{property_code}-{number}',
                'input_type' => 'text',
                'description' => 'Format for automatically generated unit numbers.',
            ],
            [
                'key' => 'property_status_options',
                'group' => 'property',
                'value' => '["active","inactive","maintenance"]',
                'input_type' => 'text',
                'description' => 'Available property status options.',
            ],
            [
                'key' => 'allow_multiple_caretakers',
                'group' => 'property',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Allow assigning multiple caretakers to a single property.',
            ],

            // Rent Settings
            [
                'key' => 'default_rent_due_date',
                'group' => 'rent',
                'value' => '5',
                'input_type' => 'number',
                'description' => 'Default day of month when rent is due.',
            ],
            [
                'key' => 'grace_period_days',
                'group' => 'rent',
                'value' => '5',
                'input_type' => 'number',
                'description' => 'Number of days grace period before late fees apply.',
            ],
            [
                'key' => 'late_payment_penalty',
                'group' => 'rent',
                'value' => '5.00',
                'input_type' => 'number',
                'description' => 'Late payment penalty percentage or fixed amount.',
            ],
            [
                'key' => 'automatic_monthly_invoices',
                'group' => 'rent',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Automatically generate rent invoices each month.',
            ],
            [
                'key' => 'rent_reminders',
                'group' => 'rent',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Send automatic rent due reminders to tenants.',
            ],
            [
                'key' => 'allow_partial_payments',
                'group' => 'rent',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Allow tenants to make partial rent payments.',
            ],
            [
                'key' => 'allow_advance_payments',
                'group' => 'rent',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Allow tenants to pay rent in advance.',
            ],
            [
                'key' => 'default_security_deposit',
                'group' => 'rent',
                'value' => '1.00',
                'input_type' => 'number',
                'description' => 'Default security deposit as multiple of monthly rent.',
            ],

            // Payment Settings
            [
                'key' => 'mpesa_paybill',
                'group' => 'payment',
                'value' => '',
                'input_type' => 'text',
                'description' => 'M-Pesa Paybill or Till number for receiving payments.',
                'is_encrypted' => false,
            ],
            [
                'key' => 'mpesa_consumer_key',
                'group' => 'payment',
                'value' => '',
                'input_type' => 'password',
                'description' => 'M-Pesa Daraja API consumer key.',
                'is_encrypted' => true,
            ],
            [
                'key' => 'mpesa_consumer_secret',
                'group' => 'payment',
                'value' => '',
                'input_type' => 'password',
                'description' => 'M-Pesa Daraja API consumer secret.',
                'is_encrypted' => true,
            ],
            [
                'key' => 'mpesa_passkey',
                'group' => 'payment',
                'value' => '',
                'input_type' => 'password',
                'description' => 'M-Pesa Daraja API passkey.',
                'is_encrypted' => true,
            ],
            [
                'key' => 'mpesa_callback_url',
                'group' => 'payment',
                'value' => '',
                'input_type' => 'text',
                'description' => 'URL where M-Pesa sends payment callbacks.',
            ],
            [
                'key' => 'bank_details',
                'group' => 'payment',
                'value' => '',
                'input_type' => 'text',
                'description' => 'Bank account details for direct transfers.',
            ],
            [
                'key' => 'auto_confirm_mpesa',
                'group' => 'payment',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Automatically confirm M-Pesa payments.',
            ],
            [
                'key' => 'send_payment_receipts',
                'group' => 'payment',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Automatically send payment receipts to tenants.',
            ],

            // Security Settings
            [
                'key' => 'password_min_length',
                'group' => 'security',
                'value' => '12',
                'input_type' => 'number',
                'description' => 'Minimum required password length.',
            ],
            [
                'key' => 'require_uppercase',
                'group' => 'security',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Require uppercase letters in passwords.',
            ],
            [
                'key' => 'session_timeout',
                'group' => 'security',
                'value' => '120',
                'input_type' => 'number',
                'description' => 'Session timeout in minutes.',
            ],
            [
                'key' => 'max_login_attempts',
                'group' => 'security',
                'value' => '5',
                'input_type' => 'number',
                'description' => 'Maximum failed login attempts before lockout.',
            ],
            [
                'key' => 'email_verification_required',
                'group' => 'security',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Require email verification before login.',
            ],
            [
                'key' => 'require_admin_approval',
                'group' => 'security',
                'value' => '0',
                'input_type' => 'checkbox',
                'description' => 'Require admin approval for new user accounts.',
            ],

            // Notification Settings
            [
                'key' => 'rent_due_reminders',
                'group' => 'notification',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Send automatic rent due reminders.',
            ],
            [
                'key' => 'overdue_rent_alerts',
                'group' => 'notification',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Send alerts when rent becomes overdue.',
            ],
            [
                'key' => 'payment_success_alerts',
                'group' => 'notification',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Notify on successful payments.',
            ],
            [
                'key' => 'payment_failed_alerts',
                'group' => 'notification',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Notify on failed payments.',
            ],
            [
                'key' => 'new_tenant_alerts',
                'group' => 'notification',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Notify when a new tenant is registered.',
            ],
            [
                'key' => 'maintenance_notifications',
                'group' => 'notification',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Notify about maintenance requests.',
            ],
            [
                'key' => 'lease_expiry_reminders',
                'group' => 'notification',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Send reminders before lease expiry.',
            ],

            // Maintenance Settings
            [
                'key' => 'maintenance_categories',
                'group' => 'maintenance',
                'value' => '["Plumbing","Electrical","HVAC","Cleaning","Repairs","Other"]',
                'input_type' => 'text',
                'description' => 'Available maintenance request categories.',
            ],
            [
                'key' => 'maintenance_priority_levels',
                'group' => 'maintenance',
                'value' => '["Low","Medium","High","Emergency"]',
                'input_type' => 'text',
                'description' => 'Available maintenance priority levels.',
            ],
            [
                'key' => 'maintenance_approval_required',
                'group' => 'maintenance',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Require admin approval for maintenance requests.',
            ],
            [
                'key' => 'maintenance_status_options',
                'group' => 'maintenance',
                'value' => '["open","in_progress","completed","cancelled"]',
                'input_type' => 'text',
                'description' => 'Available maintenance request statuses.',
            ],

            // Backup & System
            [
                'key' => 'auto_backup',
                'group' => 'backup',
                'value' => '1',
                'input_type' => 'checkbox',
                'description' => 'Enable automatic daily database backups.',
            ],
            [
                'key' => 'log_retention_days',
                'group' => 'backup',
                'value' => '90',
                'input_type' => 'number',
                'description' => 'Number of days to retain activity logs.',
            ],
        ];

        foreach ($settings as $setting) {
            SystemSetting::updateOrCreate(
                ['key' => $setting['key']],
                $setting
            );
        }
    }
}
