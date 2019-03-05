import React from 'react'
import fetch from 'isomorphic-unfetch'

class Search extends React.Component {
  constructor(props) {
    super(props)
    this.state = {value: '', error: ''}

    this.handleChange = this.handleChange.bind(this)
    this.handleSubmit = this.handleSubmit.bind(this)
  }

  async handleSubmit(event) {
  }

  handleChange(event) {
    this.setState({value: event.target.value})
  }

  render() {
    return (
      <form onSubmit={this.handleSubmit}>
        <div className="form-container">
          <input type="text" name="search" value={this.state.value} onChange={this.handleChange} placeholder="Search" />
        </div>
      </form>
    )
  }
}

export default Search
