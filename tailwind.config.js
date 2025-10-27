const defaultColors = require('tailwindcss/colors')
import preset from './vendor/filament/support/tailwind.config.preset'

module.exports = {
    presets: [preset],
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './vendor/filament/**/*.blade.php',
        "./resources/**/*.js",
        "./resources/*.blade.php",
        './app/Helpers/*.php',
        './resources/views/app/interfaces/permanence/**/*.blade.php',
        '.resources/views/vendor/*/*.blade.php'
    ],
    safelist: [
        'blur-sm',
        'hover:blur-none',
        'bg-blue-500',
        'bg-[#005F73]',
        'bg-[#90be6d]',
        'bg-[#eb7092]',
        'h-[18px]',
        'w-[18px]',
        'h-[24px]',
        'w-[24px]',
        'h-[35px]',
        'w-[35px]',
        'h-2.5', 'w-2.5',
        'h-4', 'w-4',
        'h-6', 'w-6',
        'flex',
        'items-center',
        'space-x-5'
    ],
    theme: {
        screens: {
            sm: '640px',
            md: '768px',
            lg: '1024px',
            xl: '1280px',
        },
        extend: {
            colors: {
                gray: defaultColors.slate,
                danger: defaultColors.rose,
                primary: defaultColors.blue,
                success: defaultColors.green,
                warning: defaultColors.yellow,
                'gray-light' : '#f3f3f3',
                'mc-donalds-green' : '#2b4f35',
                'corporate': {
                    'rose' : '#eb7092',
                    'blue-gray' : '#54698d',
                    'blue' : '#014486',
                    'yellow' : '#f6b852',
                    'purple' : '#828ee4',
                    'salmon' : '#f0885e',
                    'light-green' : '#5dc073',
                    'light-blue' : '#8bb7ff',
                    'sky' : '#3f8efc',
                    'green' : '#8ec649',
                    'dark-green' : '#267c6f',
                    'salmon-light' : '#e29578',
                    'blue-street' : '#1a659e',
                    'orange' : '#ff9f1c',
                    'dark-gray' : '#353531'
                },
                'night' : {
                    white : {
                        DEFAULT: '#fff',
                    },
                    dark : {
                        DEFAULT: '#2C3333',
                    },
                }
            },
        },
    },
    plugins: [
        require('@tailwindcss/forms'),
        require('@tailwindcss/typography'),
        require('tailwind-scrollbar'),
        require('@tailwindcss/aspect-ratio'),
    ],
}

