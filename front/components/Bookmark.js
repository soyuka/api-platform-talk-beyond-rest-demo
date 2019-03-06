import { useEffect } from 'react'
import shave from 'shave'

function createRuler () {
  let ruler = document.getElementById('ruler')
  if (ruler) return ruler

  ruler = document.createElement('div')
  ruler.id = 'ruler'
  ruler.style.height = '3em'
  ruler.style.visibility = 'hidden'
  document.body.appendChild(ruler)
  return ruler
}

const Bookmark = ({ bookmark, onRemove }) => {
  const ruler = createRuler()

  useEffect(() => {
    shave('h2', ruler.clientHeight)
  }, [bookmark.title])

  return <article className='bookmark col bordered'>
    <div class='image-container'>
      <img src={bookmark.screenshot ? `${process.env.api}/screenshots/${bookmark.screenshot}` : `https://via.placeholder.com/1920x1080?text=${bookmark.title ? bookmark.title : bookmark.link}`} />
    </div>
    <h2>{bookmark.title ? bookmark.title : bookmark.link}</h2>
    <a target='_blank' href={bookmark.link}>{bookmark.link}</a>
    <button className='remove' onClick={() => onRemove(bookmark.id)}>❌</button>
  </article>
}

export default Bookmark
