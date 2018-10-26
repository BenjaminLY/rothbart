const getters = {
	getLoginScreenStatus: state => {
		return state.loginScreenStatus
	},
	getTokens: state => {
		return state.tokens
	},
	getNextRoute: state => {
		return state.nextRoute
	}
}

export default getters
