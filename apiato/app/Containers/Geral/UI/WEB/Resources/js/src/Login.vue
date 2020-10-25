<template>
	<div
		id="app"
		:class="vueAppClasses"
	>
		<div id="loading-bg">
			<svg
				xmlns="http://www.w3.org/2000/svg"
				version="1.1"
			>
				<defs>
					<filter
						class="shadow"
						id="shadow"
					>
						<feDropShadow
							dx="0"
							dy="0"
							stdDeviation="2"
						></feDropShadow>
					</filter>
					<linearGradient
						id="gradient"
						x1="0%"
						y1="0%"
						x2="0%"
						y2="100%"
					>
						<stop
							offset="0%"
							style="stop-color:rgb(255,255,255);stop-opacity:1"
						></stop>
						<stop
							offset="100%"
							style="stop-color:rgb(243,243,243);stop-opacity:1"
						></stop>
					</linearGradient>
					<linearGradient
						id="bodyGradient"
						x1="0%"
						y1="100%"
						x2="0%"
						y2="0%"
					>
						<stop
							offset="0"
							stop-color="rgb(208, 41, 32)"
						>
							<animate
								begin="0s; bodyGradientAnim2.end"
								dur="2s"
								attributeName="offset"
								fill="freeze"
								from="0"
								to="1"
								id="bodyGradientAnim1"
							/>
							<animate
								begin="bodyGradientAnim1.end"
								dur="2s"
								attributeName="offset"
								fill="freeze"
								from="1"
								to="0"
								id="bodyGradientAnim2"
							/>
						</stop>
						<stop
							offset="0"
							stop-color="rgb(255,255,255)"
						>
							<animate
								begin="0s; bodyGradientAnim2.end"
								dur="2s"
								attributeName="offset"
								fill="freeze"
								from="0"
								to="1"
							/>
							<animate
								begin="bodyGradientAnim1.end"
								dur="2s"
								attributeName="offset"
								fill="freeze"
								from="1"
								to="0"
							/>
						</stop>
					</linearGradient>
				</defs>
				<path
					d="M 19 36 L 33 43 Q 29 54 33 64 L 33 64 L 17.5 73 L 5 64 L 5 43 L 19 36 Z"
					class="polly1"
					filter="url(#shadow)"
				></path>
				<path
					d="M 36 2 L 22 11 L 22 30 L 36 37 Q 41 29 53 26 L 53 10 L 36 2 Z"
					class="polly2"
					filter="url(#shadow)"
				></path>
				<path
					d="M 79 2 L 95 10.5 L 95 30 L 79 37 Q 71.5 29 62 26 L 62 10 L 79 2 Z"
					class="polly3"
					filter="url(#shadow)"
				></path>
				<path
					d="M 98 36 L 113 43 L 113 64 L 98 72 L 83 64 Q 87 54 83 43 L 98 36 Z"
					class="polly4"
					filter="url(#shadow)"
				></path>
				<path
					d="M 61 32 Q 68 34 72 38.5 Q 78 46 77 56 Q 75.3 64.8 69.5 69 Q 64.2 74.7 51.5 73 Q 44.1 70.4 40 64.5 Q 35 59 37 46.5 Q 39.6 39.1 45.5 35 Q 52 31 61 32 Z"
					class="head"
					fill="url(#gradient)"
					filter="url(#shadow)"
				></path>
				<path
					d="M 36 70 Q 57 91 80 70 L 95 78.5 L 95 98 L 79 108 L 70 103 L 70 156 L 46 156 L 46 103 L 37 108 L 21 98 L 21 79 L 36 70 Z"
					class="body"
					fill="url(#bodyGradient)"
					filter="url(#shadow)"
				></path>
			</svg>
			<div class="loadingText">Carregando...</div>
		</div>
		<router-view @setAppClasses="setAppClasses" />
	</div>
</template>

<script>
import themeConfig from "@/../themeConfig.js";
import jwt from "@/http/requests/auth/jwt/index.js";

export default {
	data() {
		return {
			vueAppClasses: []
		};
	},
	watch: {
		"$store.state.theme"(val) {
			this.toggleClassInBody(val);
		},
		"$vs.rtl"(val) {
			document.documentElement.setAttribute("dir", val ? "rtl" : "ltr");
		}
	},
	methods: {
		toggleClassInBody(className) {
			if (className == "dark") {
				if (document.body.className.match("theme-semi-dark"))
					document.body.classList.remove("theme-semi-dark");
				document.body.classList.add("theme-dark");
			} else if (className == "semi-dark") {
				if (document.body.className.match("theme-dark"))
					document.body.classList.remove("theme-dark");
				document.body.classList.add("theme-semi-dark");
			} else {
				if (document.body.className.match("theme-dark"))
					document.body.classList.remove("theme-dark");
				if (document.body.className.match("theme-semi-dark"))
					document.body.classList.remove("theme-semi-dark");
			}
		},
		setAppClasses(classesStr) {
			this.vueAppClasses.push(classesStr);
		},
		handleWindowResize() {
			this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);

			// Set --vh property
			document.documentElement.style.setProperty(
				"--vh",
				`${window.innerHeight * 0.01}px`
			);
		},
		handleScroll() {
			this.$store.commit("UPDATE_WINDOW_SCROLL_Y", window.scrollY);
		}
	},
	mounted() {
		this.toggleClassInBody(themeConfig.theme);
		this.$store.commit("UPDATE_WINDOW_WIDTH", window.innerWidth);

		let vh = window.innerHeight * 0.01;
		// Then we set the value in the --vh custom property to the root of the document
		document.documentElement.style.setProperty("--vh", `${vh}px`);
	},
	async created() {
		// jwt
		jwt.init();

		let dir = this.$vs.rtl ? "rtl" : "ltr";
		document.documentElement.setAttribute("dir", dir);

		window.addEventListener("resize", this.handleWindowResize);
		window.addEventListener("scroll", this.handleScroll);
	},
	destroyed() {
		window.removeEventListener("resize", this.handleWindowResize);
		window.removeEventListener("scroll", this.handleScroll);
	}
};
</script>
