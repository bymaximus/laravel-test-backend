const mix = require('laravel-mix');
let tailwindcss = require('tailwindcss');
const ChunkRenamePlugin = require('webpack-chunk-rename-plugin');
const HardSourceWebpackPlugin = require('hard-source-webpack-plugin');
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

var cachePrune = {
	maxAge: 2 * 24 * 60 * 60 * 1000,
	sizeThreshold: 50 * 1024 * 1024
};
if (process.env.hasOwnProperty('npm_config_nocache') &&
	process.env.npm_config_nocache
) {
	cachePrune.maxAge = 1;
}

var buildPath = '../../../../../public';
var containerName = 'login';

mix.options({
	clearConsole: false,
	postCss: [
		require('autoprefixer'),
		require('postcss-rtl'),
		tailwindcss(path.resolve(__dirname, './tailwind.js')),
	],
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
			chunkFilename: 'container/admin/js/' + containerName + '/chunks/[name].js?id=[chunkhash]',
			filename: (chunkData) => {
				if (chunkData.chunk.hasOwnProperty('name')) {
					if (chunkData.chunk.name == '/container/admin/js/' + containerName) {
						return 'container/admin/js/' + containerName + '.js?id=[chunkhash]';
					} else if (chunkData.chunk.name == '/container/admin/js/manifest') {
						return 'container/admin/js/' + containerName + '/manifest.js?id=[chunkhash]';
					}
				}
				return '[name].js';
			},
		},
		plugins: [
			new ChunkRenamePlugin({
				initialChunksWithEntry: true,
				'/container/admin/js/vendor': 'container/admin/js/' + containerName + '/vendor.js?id=[chunkhash]',
			}),
			new HardSourceWebpackPlugin({
				info: {
					mode: 'none',
					level: 'debug',
				},
				cacheDirectory: path.resolve(ramDiskPlugin.diskPath, './node_modules/.cache/hard-source/[confighash]'),
				environmentHash: {
					//root: process.cwd(),
					root: path.resolve(__dirname, '../../../../../'),
					directories: [],
					files: ['package-lock.json', 'yarn.lock']
				},
				cachePrune: cachePrune,
			}),
		],
	})
	.js(path.resolve(__dirname, './Resources/js/' + containerName + '.js'), path.resolve(__dirname, '../../../../../public/container/admin/js/' + containerName + '.js'))
	.sourceMaps()
	.extract();
