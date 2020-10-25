
export default [
	// {
	//   url: "/apps/email",
	//   name: "Email",
	//   slug: "email",
	//   icon: "MailIcon",
	//   i18n: "Email",
	// },
	{
		url: '/dashboard',
		name: "Dashboard",
		slug: "dashboard-analytics",
		tag: "2",
		tagColor: "warning",
		icon: "HomeIcon",
		i18n: "Dashboard"
	},
	{
		url: null,
		name: "Sorologia",
		icon: "HomeIcon",
		i18n: "Sorologia",
		submenu: [{
			url: null,
			name: "Cadastros",
			slug: "sorologia-cadastro",
			i18n: "Cadastros",
			submenu: [{
					url: '/sorologia/cadastro/bancodesangue',
					name: "Banco de Sangue",
					i18n: "Banco de Sangue",
					slug: "sorologia-cadastro-bancodesangue",
				},
				{
					url: '/sorologia/cadastro/exame',
					name: "Exames",
					i18n: "Exames",
					slug: "sorologia-cadastro-exame",
				},
			]
		}]
	},
	{
		url: null,
		name: "Segurança",
		icon: "LockIcon",
		i18n: "Segurança",
		submenu: [{
				url: '/seguranca/perfil',
				name: "Perfil",
				i18n: "Perfil",
				slug: "seguranca-perfil",
			},
			{
				url: '/seguranca/usuario',
				name: "Usuário",
				i18n: "Usuário",
				slug: "seguranca-usuario",
			},
			{
				url: '/seguranca/funcionalidade',
				name: "Funcionalidade",
				i18n: "Funcionalidade",
				slug: "seguranca-funcionalidade",
			},
			{
				url: '/seguranca/configuracao',
				name: "Configurações",
				i18n: "Configurações",
				slug: "seguranca-configuracao",
			}
		]
	},
	{
		url: null,
		name: "Customers",
		icon: "UserIcon",
		i18n: "Customers",
		submenu: [{
				url: '/customers/active',
				name: "Active",
				slug: "customers-active",
				i18n: "Active",
			},
			{
				url: '/customers/pending',
				name: "Pending",
				slug: "customers-pending",
				i18n: "Pending",
			},
			{
				url: '/customers/inactive',
				name: "Inactive",
				slug: "customers-inactive",
				i18n: "Inactive",
			},
		]
	},
	{
		url: null,
		name: "Merchants",
		icon: "UsersIcon",
		i18n: "Merchants",
		submenu: [{
				url: '/settings/merchant/active',
				name: "Active",
				slug: "merchant-active",
				i18n: "Active",
				icon: "UnlockIcon",
			},
			{
				url: '/settings/merchant/pending',
				name: "Pending",
				slug: "merchant-pending",
				i18n: "Pending",
				icon: "ClockIcon",
			},
			{
				url: '/settings/merchant/inactive',
				name: "Inactive",
				slug: "merchant-inactive",
				i18n: "Inactive",
				icon: "LockIcon",
			},
		]
	},
	{
		url: '/settings/subscription',
		name: "Subscriptions",
		icon: "ClockIcon",
		i18n: "Subscriptions",
		slug: "subscriptions",
	},
	{
		url: '/settings/charge',
		name: "Charges",
		icon: "ShoppingCartIcon",
		i18n: "Charges",
		slug: "charges",
	},
	{
		header: "Settings",
		icon: "SettingsIcon",
		i18n: "Settings",
		items: [{
				url: '/settings/general',
				name: "General",
				slug: "settings-general",
				i18n: "General",
				icon: "ListIcon",
			},
			{
				url: '/settings/admins',
				name: "Administrators",
				slug: "settings-admins",
				i18n: "Administrators",
				icon: "UsersIcon",
			},
			{
				url: '/settings/plan',
				name: "Plans",
				slug: "settings-plan",
				i18n: "Plans",
				icon: "PackageIcon",
			},
			{
				url: '/settings/emailTemplate',
				name: "Email Template",
				slug: "settings-email-template",
				i18n: "Email Template",
				icon: "MailIcon",
			},
			{
				url: '/settings/country',
				name: "Countries",
				slug: "settings-country",
				i18n: "Countries",
				icon: "FlagIcon",
			},
			{
				url: '/settings/currency',
				name: "Currencies",
				slug: "settings-currency",
				i18n: "Currencies",
				icon: "DollarSignIcon",
			},
			{
				url: '/settings/gateway',
				name: "Gateways",
				slug: "settings-gateway",
				i18n: "Gateways",
				icon: "ShuffleIcon",
			},
			{
				url: '/settings/fraudCheck',
				name: "Fraud Check",
				slug: "settings-fraud-check",
				i18n: "Fraud Check",
				icon: "CheckSquareIcon",
			},
			{
				url: null,
				name: "Banned",
				i18n: "Banned",
				icon: "FrownIcon",
				submenu: [{
						url: '/settings/bannedEmail',
						name: "Email",
						slug: "settings-banned-email",
						i18n: "Email",
					},
					{
						url: '/settings/bannedCreditCard',
						name: "Credit Card",
						slug: "settings-banned-credit-card",
						i18n: "Credit Card",
						icon: "CreditCardIcon",
					},
				]
			},
		]
	},
];
