const mix = require('laravel-mix');
let tailwindcss = require('tailwindcss');
const {
	WebpackPluginRamdisk
} = require('webpack-plugin-ramdisk');
const {
	existsSync,
	rmdirSync
} = require('fs');
const execa = require('execa');
require('laravel-mix-merge-manifest');

const ramDiskOptions = {
	blockSize: 512,
	// 256 mb
	//bytes: 2.56e8,
	bytes: 5.12e8,
	name: 'accordousBuild'
};
const mountPath = path.resolve('/mnt/', './', ramDiskOptions.name);
if (existsSync(mountPath)) {
	try {
		const {
			stdout: isMounted
		} = execa.commandSync("sudo mount |awk '{print $3}'| grep -w " + `${mountPath}`, {
			shell: true
		});
		if (!isMounted ||
			!isMounted.trim()
		) {
			rmdirSync(mountPath);
		}
	} catch (error) {
		rmdirSync(mountPath);
	}
}
const ramDiskPlugin = new WebpackPluginRamdisk(ramDiskOptions);

var buildPath = '../../../../../public';

mix.options({
	clearConsole: false
});

mix.setPublicPath(buildPath).mergeManifest();

mix.babelConfig({
		cacheDirectory: path.resolve(ramDiskPlugin.diskPath, './node_modules/.cache/babel-loader')
	})
	.webpackConfig({
		resolve: {
			alias: {
				'@': path.resolve(__dirname, './Resources/js/src'),
				'@assets': path.resolve(__dirname, './Resources/assets'),
				'@sass': path.resolve(__dirname, './Resources/sass'),
				'@module': path.resolve(__dirname, './Resources/js/src')
			},
			modules: [
            path.resolve(__dirname, '../../../../../node_modules'),
            'node_modules'
        ]
		},
		output: {
			path: path.resolve(__dirname, buildPath),
		},
	})
	.sass(path.resolve(__dirname, './Resources/sass/app.scss'), path.resolve(__dirname, '../../../../../public/container/admin/css')).options({
		postCss: [require('autoprefixer'), require('postcss-rtl')]
	})
	.postCss(path.resolve(__dirname, './Resources/assets/css/main.css'), path.resolve(__dirname, '../../../../../public/container/admin/css'), [
        tailwindcss(path.resolve(__dirname, './tailwind.js')), require('postcss-rtl')()
    ])
	.copy(path.resolve(__dirname, '../../../../../node_modules/vuesax/dist/vuesax.css'), path.resolve(__dirname, '../../../../../public/css/vuesax.css'))
	.copy(path.resolve(__dirname, './Resources/assets/css/iconfont.css'), path.resolve(__dirname, '../../../../../public/css/iconfont.css'))
	.copy(path.resolve(__dirname, './Resources/assets/css/loader.css'), path.resolve(__dirname, '../../../../../public/css/loader.css'))
	.copyDirectory(path.resolve(__dirname, './Resources/assets/fonts'), path.resolve(__dirname, '../../../../../public/fonts'))
	.copyDirectory(path.resolve(__dirname, '../../../../../node_modules/material-icons/iconfont'), path.resolve(__dirname, '../../../../../public/css/material-icons'))
	.copyDirectory(path.resolve(__dirname, '../../../../../node_modules/material-icons/iconfont/material-icons.css'), path.resolve(__dirname, '../../../../../public/css/material-icons/material-icons.css'))
	.copy(path.resolve(__dirname, '../../../../../node_modules/prismjs/themes/prism-tomorrow.css'), path.resolve(__dirname, '../../../../../public/css/prism-tomorrow.css'))
	.copyDirectory(path.resolve(__dirname, './Resources/assets/images'), path.resolve(__dirname, '../../../../../public/images'))
	.sass(path.resolve(__dirname, './Resources/sass/login.scss'), path.resolve(__dirname, '../../../../../public/container/admin/css/login.css'))
	.sourceMaps();
mix.version();
