const Alert = ({ error }) => {
  if (!error) {
    return null
  }

  return <div className='alert alert-error'>{error}</div>
}

export default Alert
