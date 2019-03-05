import shave from 'shave'
import React, { Fragment  } from "react";

class Bookmark extends React.Component {
  constructor(props) {
    super(props)
  }

  createRuler() {
    let ruler = document.getElementById('ruler')
    if (ruler) return ruler

    ruler = document.createElement('div')
    ruler.id = 'ruler'
    ruler.style.height = '3em'
    ruler.style.visibility = 'hidden'
    document.body.appendChild(ruler)
    return ruler
  }

  render() {
    const bookmark = this.props.bookmark
    return (
      <article className="bookmark col bordered">
        {bookmark.screenshot && <img src={`${process.env.api}/screenshots/${bookmark.screenshot}`} />}
        {!bookmark.screenshot && <img src={`https://via.placeholder.com/1920x1080?text=${bookmark.title}`} />}
        <h2>{bookmark.title ? bookmark.title : bookmark.link}</h2>
        <a href={bookmark.link}>{bookmark.link}</a>
      </article>
    )
  }

  componentDidUpdate() {
    const ruler = this.createRuler()
    shave('h2', ruler.clientHeight)
  }

  componentDidMount() {
    const ruler = this.createRuler()
    shave('h2', ruler.clientHeight)
  }
}

export default Bookmark
