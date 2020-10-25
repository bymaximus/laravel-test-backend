// axios
import axios from 'axios'

export default axios.create({
  	baseURL: "//" +
  		"backend." +
  		window.document.location.hostname +
  		(window.document.location.port ?
  			":" + window.document.location.port.toString() :
  			"")
})
