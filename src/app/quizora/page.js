"use client"
import Footer from "@/components/Footer";
import Loader from "@/components/Loader";
import Navbar from "@/components/Navbar";
import baseUrl from "@/Services/BaseUrl";
import axios from "axios";
import Cookies from "js-cookie";
import Link from "next/link";
import { Fragment, useEffect, useState } from "react";
import { useRouter } from "next/navigation";
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';
import BasicPie from './BasicPie'


const Page = () => {
  const router = useRouter()
  const [loading, setLoading] = useState(false);
  const [allData, setAllData] = useState([])
  const [savedAllData, setSavedAllData] = useState([])
  const [status, setStatus] = useState('') // Default to empty string for "All" tab
  const [result, setResult] = useState()
  const [resultQuiz, setResultQuiz] = useState()
  const [quizType, setQuizType] = useState('status')
  const [open, setOpen] = useState(false);
  const handleClickOpen = () => setOpen(true);
  const handleClose = () => setOpen(false);

  useEffect(() => {
    const IsUserExist = Cookies.get('user-token')
    setLoading(true);
    if (!IsUserExist) {
      return router.push('/')
    } else {
      let saved = sessionStorage.getItem('is_saved')
      if (saved) {
        setStatus('saved')
        setQuizType('saved')
        setTimeout(() => {
          sessionStorage.removeItem('is_saved')
        }, 1000);
      } else {
        // Load All quizzes by default
        getData()
      }
    }
  }, []);

  useEffect(() => {
    setLoading(true);
    if (status === 'saved') {
      setQuizType('saved')
      getDataSaved()
    } else {
      setQuizType('status')
      getData()
    }
  }, [status]);

  const getData = async () => {
    const cookies = Cookies.get('user-token');
    try {
      axios.post(`${baseUrl}/api/quiz/categories?quiz_status=${status}`, {}, {
        headers: {
          'Authorization': `Bearer ${cookies}`
        }
      }).then((response) => {
        setAllData(response.data.data);
        setLoading(false);
      })
    } catch (error) {
      console.error(error);
      setLoading(false);
    }
  }

  const getDataSaved = async () => {
    const cookies = Cookies.get('user-token');
    const user_id = Cookies.get('user-id');
    try {
      let response = await axios.post(`${baseUrl}/api/quiz/bookmarks`, { user_id }, {
        headers: {
          'Authorization': `Bearer ${cookies}`
        }
      })

      // return console.log(response)

      setSavedAllData(response.data.data.list.data);
      setLoading(false);
    } catch (error) {
      console.error(error);
      setLoading(false);
    }
  }

  const getResult = async (id, quiz) => {
    setResultQuiz(quiz)
    const cookies = Cookies.get('user-token');
    try {
      axios.post(`${baseUrl}/api/quiz/result`, { quiz_id: id }, {
        headers: {
          'Authorization': `Bearer ${cookies}`
        }
      }).then((response) => {
        setResult(response.data.data);
        handleClickOpen(true)
      })
    } catch (error) {
      console.error(error);
      setLoading(false);
    }
  }

  return (
    <>
      <Navbar />
      {!loading ? (
        <div className="inner-page">
          <div className="Quiz-wrapper">
            <style jsx>{`
              .modern-tabs {
                background: linear-gradient(180deg, #1B1E27 0%, #0F1116 100%);
                padding: 30px 0 20px;
                border-bottom: 1px solid rgba(255, 255, 255, 0.1);
              }
              .modern-tabs .nav-tabs {
                border: none;
                gap: 12px;
                flex-wrap: wrap;
                justify-content: center;
              }
              .modern-tabs .nav-item {
                margin: 0;
              }
              .modern-tabs .nav-link {
                background: rgba(255, 255, 255, 0.05);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 12px;
                color: rgba(255, 255, 255, 0.7);
                padding: 12px 28px;
                font-size: 15px;
                font-weight: 500;
                font-family: 'Poppins', sans-serif;
                transition: all 0.3s ease;
                cursor: pointer;
                position: relative;
                overflow: hidden;
              }
              .modern-tabs .nav-link:hover {
                background: rgba(68, 166, 197, 0.15);
                color: #44A6C5;
                border-color: rgba(68, 166, 197, 0.3);
                transform: translateY(-2px);
              }
              .modern-tabs .nav-link.active {
                background: linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%);
                border-color: #44A6C5;
                color: white;
                box-shadow: 0 4px 15px rgba(68, 166, 197, 0.4);
                font-weight: 600;
              }
              .quiz-content-area {
                background: #0F1116;
                min-height: 70vh;
                padding: 40px 0;
              }
              .modern-quiz-card {
                background: linear-gradient(135deg, rgba(27, 30, 39, 0.95) 0%, rgba(15, 17, 22, 0.95) 100%);
                border: 1px solid rgba(255, 255, 255, 0.1);
                border-radius: 20px;
                padding: 25px;
                margin-bottom: 20px;
                transition: all 0.3s ease;
                position: relative;
                overflow: hidden;
              }
              .modern-quiz-card::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                width: 4px;
                height: 100%;
                background: linear-gradient(180deg, #44A6C5, #1E4FFD);
                opacity: 0;
                transition: opacity 0.3s ease;
              }
              .modern-quiz-card:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 30px rgba(68, 166, 197, 0.2);
                border-color: rgba(68, 166, 197, 0.3);
              }
              .modern-quiz-card:hover::before {
                opacity: 1;
              }
              .quiz-category-title {
                color: #44A6C5;
                font-size: 22px;
                font-weight: 700;
                font-family: 'Poppins', sans-serif;
                margin-bottom: 25px;
                padding-bottom: 15px;
                border-bottom: 2px solid rgba(68, 166, 197, 0.3);
                display: flex;
                align-items: center;
                gap: 12px;
              }
              .quiz-category-title::before {
                content: '';
                width: 6px;
                height: 30px;
                background: linear-gradient(180deg, #44A6C5, #1E4FFD);
                border-radius: 3px;
              }
              .quiz-number-badge {
                position: absolute;
                top: 20px;
                left: 20px;
                width: 45px;
                height: 45px;
                background: linear-gradient(135deg, #44A6C5, #1E4FFD);
                border-radius: 12px;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: 700;
                font-size: 18px;
                color: white;
                box-shadow: 0 4px 15px rgba(68, 166, 197, 0.3);
                font-family: 'Poppins', sans-serif;
              }
              .quiz-image-container {
                border-radius: 15px;
                overflow: hidden;
                box-shadow: 0 4px 20px rgba(0, 0, 0, 0.3);
                transition: transform 0.3s ease;
              }
              .quiz-image-container:hover {
                transform: scale(1.05);
              }
              .quiz-status-badge {
                padding: 8px 18px;
                border-radius: 25px;
                font-size: 13px;
                font-weight: 600;
                font-family: 'Poppins', sans-serif;
                display: inline-flex;
                align-items: center;
                gap: 8px;
                margin-bottom: 15px;
                border: none;
                text-transform: uppercase;
                letter-spacing: 0.5px;
              }
              .quiz-status-badge.unattempted {
                background: linear-gradient(135deg, rgba(255, 152, 0, 0.2), rgba(255, 152, 0, 0.1));
                color: #FFA726;
                border: 1px solid rgba(255, 152, 0, 0.3);
              }
              .quiz-status-badge.completed {
                background: linear-gradient(135deg, rgba(76, 175, 80, 0.2), rgba(76, 175, 80, 0.1));
                color: #66BB6A;
                border: 1px solid rgba(76, 175, 80, 0.3);
              }
              .quiz-status-badge.paused {
                background: linear-gradient(135deg, rgba(68, 166, 197, 0.2), rgba(68, 166, 197, 0.1));
                color: #44A6C5;
                border: 1px solid rgba(68, 166, 197, 0.3);
              }
              .quiz-title {
                color: white;
                font-size: 20px;
                font-weight: 600;
                font-family: 'Poppins', sans-serif;
                margin-bottom: 10px;
                line-height: 1.4;
              }
              .quiz-questions-label {
                color: rgba(255, 255, 255, 0.6);
                font-size: 15px;
                font-family: 'Poppins', sans-serif;
                display: flex;
                align-items: center;
                gap: 8px;
              }
              .quiz-questions-label::before {
                content: '📝';
                font-size: 18px;
              }
              .quiz-action-btn {
                background: linear-gradient(92.48deg, #44A6C5 3.13%, #1E4FFD 100%);
                color: white;
                border: none;
                border-radius: 12px;
                padding: 14px 28px;
                font-size: 15px;
                font-weight: 600;
                font-family: 'Poppins', sans-serif;
                cursor: pointer;
                transition: all 0.3s ease;
                width: 100%;
                margin-bottom: 10px;
                box-shadow: 0 4px 15px rgba(68, 166, 197, 0.3);
              }
              .quiz-action-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(68, 166, 197, 0.4);
              }
              .quiz-result-btn {
                background: linear-gradient(135deg, #FFA726, #FB8C00);
                color: white;
                border: none;
                border-radius: 12px;
                padding: 14px 28px;
                font-size: 15px;
                font-weight: 600;
                font-family: 'Poppins', sans-serif;
                cursor: pointer;
                transition: all 0.3s ease;
                width: 100%;
                margin-bottom: 10px;
                box-shadow: 0 4px 15px rgba(255, 167, 38, 0.3);
              }
              .quiz-result-btn:hover {
                transform: translateY(-2px);
                box-shadow: 0 6px 20px rgba(255, 167, 38, 0.4);
              }
              .no-data-message {
                text-align: center;
                padding: 60px 20px;
                color: rgba(255, 255, 255, 0.5);
                font-size: 18px;
                font-family: 'Poppins', sans-serif;
              }
            `}</style>
            <div className="modern-tabs">
              <div className="container">
                <div className="row">
                  <div className="col-lg-12">
                    <ul className="nav nav-tabs" id="myTab" role="tablist">
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === '' ? 'active' : ''}`}
                          aria-controls="all"
                          aria-selected={status === ''}
                          onClick={() => setStatus('')}
                        >
                          <i className="fa-solid fa-list-ul" style={{ marginRight: '8px' }} />
                          All
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'paused' ? 'active' : ''}`}
                          aria-controls="paused"
                          aria-selected={status === 'paused'}
                          onClick={() => setStatus('paused')}
                        >
                          <i className="fa-solid fa-pause" style={{ marginRight: '8px' }} />
                          Paused
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'completed' ? 'active' : ''}`}
                          aria-controls="completed"
                          aria-selected={status === 'completed'}
                          onClick={() => setStatus('completed')}
                        >
                          <i className="fa-solid fa-circle-check" style={{ marginRight: '8px' }} />
                          Completed
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'unattempted' ? 'active' : ''}`}
                          aria-controls="unattempted"
                          aria-selected={status === 'unattempted'}
                          onClick={() => setStatus('unattempted')}
                        >
                          <i className="fa-solid fa-hourglass-start" style={{ marginRight: '8px' }} />
                          Unattempted
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'free' ? 'active' : ''}`}
                          aria-controls="free"
                          aria-selected={status === 'free'}
                          onClick={() => setStatus('free')}
                        >
                          <i className="fa-solid fa-gift" style={{ marginRight: '8px' }} />
                          Free
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'saved' ? 'active' : ''}`}
                          aria-controls="saved"
                          aria-selected={status === 'saved'}
                          onClick={() => setStatus('saved')}
                        >
                          <i className="fa-solid fa-bookmark" style={{ marginRight: '8px' }} />
                          Saved
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div className="quiz-content-area">
              <div className="container">
                <div className="row">
                  <div className="col-lg-12">
                    <section style={{ padding: '0' }}>
                      <div className="tab-content" id="myTabContent">
                        <div
                          className="tab-pane fade show active"
                          id="all"
                          role="tabpanel"
                          aria-labelledby="all-tab"
                        >
                          {allData?.length === 0 && savedAllData?.length === 0 ? (
                            <div className="no-data-message">
                              <i className="fa-solid fa-inbox" style={{ fontSize: '48px', marginBottom: '20px', display: 'block', opacity: 0.3 }} />
                              <div>No quizzes found</div>
                            </div>
                          ) : (
                            <>
                              {loading ? (
                                'loading'
                              ) : (
                                <>
                                  {quizType === 'status' ? (
                                    <>
                                      {allData?.map(category => (
                                        <Fragment key={category?.id}>
                                          {category?.quizzes?.length && (
                                            <div key={category?.id} style={{ marginBottom: '50px' }}>
                                              <h3 className="quiz-category-title">
                                                {category?.name}
                                              </h3>
                                              {category?.quizzes?.map((quiz, i) => (
                                                <div key={quiz?.id} className="modern-quiz-card">
                                                  <span className="quiz-number-badge">{i + 1}</span>
                                                  <div className="row align-items-center" style={{ paddingLeft: '70px' }}>
                                                    <div className="col-lg-2 col-md-3 col-sm-12 mb-3 mb-md-0">
                                                      <div className="quiz-image-container">
                                                        <img
                                                          src={quiz?.image_url ? quiz?.image_url : "/images/quiz.webp"}
                                                          className="img-fluid"
                                                          alt={quiz?.name}
                                                          style={{ width: '100%', height: 'auto', display: 'block' }}
                                                        />
                                                      </div>
                                                    </div>
                                                    <div className="col-lg-7 col-md-5 col-sm-12 mb-3 mb-md-0">
                                                      <div>
                                                        {quiz?.quiz_status === 0 ? (
                                                          <span className="quiz-status-badge unattempted">
                                                            <i className="fa-solid fa-hourglass-start" />
                                                            Unattempted
                                                          </span>
                                                        ) : quiz?.quiz_status === 1 ? (
                                                          <span className="quiz-status-badge completed">
                                                            <i className="fa-solid fa-circle-check" />
                                                            Completed
                                                          </span>
                                                        ) : quiz?.quiz_status === 2 ? (
                                                          <span className="quiz-status-badge paused">
                                                            <i className="fa-solid fa-pause" />
                                                            Paused
                                                          </span>
                                                        ) : (
                                                          <span className="quiz-status-badge">{status}</span>
                                                        )}
                                                        <h5 className="quiz-title">
                                                          {quiz?.name}
                                                        </h5>
                                                        <span className="quiz-questions-label">
                                                          {quiz?.quiz_status === 2 ? `${quiz?.total_questions - quiz?.unattemptedQuestionsCount}/` : ''}{quiz?.total_questions}{" "}{quiz?.total_questions > 1 ? 'Questions' : 'Question'}
                                                        </span>
                                                      </div>
                                                    </div>
                                                    <div className="col-lg-3 col-md-4 col-sm-12">
                                                      <div>
                                                        {quiz?.quiz_status === 1 ? (
                                                          <button onClick={() => getResult(quiz.id, quiz)} className="quiz-result-btn">
                                                            <i className="fa-solid fa-chart-pie" style={{ marginRight: '8px' }} />
                                                            See Result
                                                          </button>
                                                        ) : null}
                                                        <Link href={`/quiz?id=${quiz?.id}`} className="quiz-action-btn" style={{ textDecoration: 'none', display: 'block' }}>
                                                          {quiz?.quiz_status === 0 ? (
                                                            <span><i className="fa-solid fa-play" style={{ marginRight: '8px' }} />Start Quiz</span>
                                                          ) : quiz?.quiz_status === 1 ? (
                                                            <span><i className="fa-solid fa-rotate-right" style={{ marginRight: '8px' }} />Start Again</span>
                                                          ) : quiz?.quiz_status === 2 ? (
                                                            <span><i className="fa-solid fa-forward" style={{ marginRight: '8px' }} />Continue</span>
                                                          ) : (
                                                            <span><i className="fa-solid fa-play" style={{ marginRight: '8px' }} />Start Quiz</span>
                                                          )}
                                                        </Link>
                                                      </div>
                                                    </div>
                                                  </div>
                                                </div>
                                              ))}
                                            </div>
                                          )}
                                        </Fragment>
                                      ))}
                                    </>

                                  ) : (
                                    <>
                                      {savedAllData?.map((quiz, i) => (
                                        <Fragment key={quiz?.id}>
                                          <div className="modern-quiz-card">
                                            <span className="quiz-number-badge">{i + 1}</span>
                                            <div className="row align-items-center" style={{ paddingLeft: '70px' }}>
                                              <div className="col-lg-2 col-md-3 col-sm-12 mb-3 mb-md-0">
                                                <div className="quiz-image-container">
                                                  <img
                                                    src={quiz?.image_url ? quiz?.image_url : "/images/quiz.webp"}
                                                    className="img-fluid"
                                                    alt={quiz?.quiz?.name}
                                                    style={{ width: '100%', height: 'auto', display: 'block' }}
                                                  />
                                                </div>
                                              </div>
                                              <div className="col-lg-7 col-md-5 col-sm-12 mb-3 mb-md-0">
                                                <div>
                                                  {quiz?.quiz_status === 0 ? (
                                                    <span className="quiz-status-badge unattempted">
                                                      <i className="fa-solid fa-hourglass-start" />
                                                      Unattempted
                                                    </span>
                                                  ) : quiz?.quiz_status === 1 ? (
                                                    <span className="quiz-status-badge completed">
                                                      <i className="fa-solid fa-circle-check" />
                                                      Completed
                                                    </span>
                                                  ) : quiz?.quiz_status === 2 ? (
                                                    <span className="quiz-status-badge paused">
                                                      <i className="fa-solid fa-pause" />
                                                      Paused
                                                    </span>
                                                  ) : (
                                                    <span className="quiz-status-badge">{status}</span>
                                                  )}
                                                  <h5 className="quiz-title">
                                                    {quiz?.quiz?.name}
                                                  </h5>
                                                  <span className="quiz-questions-label">
                                                    {quiz?.quiz_status === 2 ? `${quiz?.total_questions - quiz?.unattemptedQuestionsCount}/` : ''}{quiz?.total_questions}{" "}{quiz?.total_questions > 1 ? 'Questions' : 'Question'}
                                                  </span>
                                                </div>
                                              </div>
                                              <div className="col-lg-3 col-md-4 col-sm-12">
                                                <div>
                                                  {quiz?.quiz_status === 1 ? (
                                                    <button onClick={() => getResult(quiz.id, quiz)} className="quiz-result-btn">
                                                      <i className="fa-solid fa-chart-pie" style={{ marginRight: '8px' }} />
                                                      See Result
                                                    </button>
                                                  ) : null}
                                                  <Link href={`/quiz?id=${quiz?.quiz?.id}`} className="quiz-action-btn" style={{ textDecoration: 'none', display: 'block' }}>
                                                    {quiz?.quiz_status === 0 ? (
                                                      <span><i className="fa-solid fa-play" style={{ marginRight: '8px' }} />Start Quiz</span>
                                                    ) : quiz?.quiz_status === 1 ? (
                                                      <span><i className="fa-solid fa-rotate-right" style={{ marginRight: '8px' }} />Start Again</span>
                                                    ) : quiz?.quiz_status === 2 ? (
                                                      <span><i className="fa-solid fa-forward" style={{ marginRight: '8px' }} />Continue</span>
                                                    ) : (
                                                      <span><i className="fa-solid fa-play" style={{ marginRight: '8px' }} />Start Quiz</span>
                                                    )}
                                                  </Link>
                                                </div>
                                              </div>
                                            </div>
                                          </div>
                                        </Fragment>
                                      ))}
                                    </>
                                  )}

                                </>
                              )}
                            </>
                          )}
                        </div>
                        <div
                          className="tab-pane fade"
                          id="paused"
                          role="tabpanel"
                          aria-labelledby="paused-tab"
                        >
                          <p>This is the content for Paused.</p>
                        </div>
                        <div
                          className="tab-pane fade"
                          id="completed"
                          role="tabpanel"
                          aria-labelledby="completed-tab"
                        >
                          <p>This is the content for Completed.</p>
                        </div>
                        <div
                          className="tab-pane fade"
                          id="unattempted"
                          role="tabpanel"
                          aria-labelledby="unattempted-tab"
                        >
                          <p>This is the content for Unattempted.</p>
                        </div>
                        <div
                          className="tab-pane fade"
                          id="free"
                          role="tabpanel"
                          aria-labelledby="free-tab"
                        >
                          <p>This is the content for Free.</p>
                        </div>
                      </div>
                    </section>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      ) : (
        <Loader />
      )}
      <Footer />

      <Dialog
        open={open}
        onClose={handleClose}
        aria-labelledby="alert-dialog-title"
        aria-describedby="alert-dialog-description"
      >
        <button
          onClick={handleClose}
          style={{
            position: 'absolute',
            top: '10px',
            right: '10px',
            background: '#f44336',
            border: 'none',
            borderRadius: '50%',
            width: '32px',
            height: '32px',
            display: 'flex',
            justifyContent: 'center',
            alignItems: 'center',
            fontSize: '1.5rem',
            cursor: 'pointer',
            color: 'white',
            fontWeight: 'bold',
            boxShadow: '0 2px 5px rgba(0, 0, 0, 0.2)',
            zIndex: 100
          }}
          aria-label="Close"
        >
          &times;
        </button>
        <DialogTitle id="alert-dialog-title" style={{ textAlign: 'center', marginBottom: '20px', color: '#2c4a87' }}>
          Quiz: {resultQuiz?.name}
        </DialogTitle>
        <p style={{ textAlign: 'center', fontSize: '18px', margin: '20px 0' }}>
          Congratulations! 🎉 You have successfully submitted the quiz. Well done! Check your results to see how you performed.
        </p>
        <DialogContent>
          <DialogContentText id="alert-dialog-description">
            <div className="quiz-result-wrapper" >
              <div><span className="quiz-result-heading">Total Questions:</span>{" "}<span className="quiz-result-ans-heading">{result?.total_questions}</span></div>
              <div> <span className="quiz-result-heading">Attempted Questions:</span>{" "}<span className="quiz-result-ans-heading">{result?.attempted_questions}</span></div>
              <div>  <span className="quiz-result-heading">Correct Answers:</span>{" "}<span className="quiz-result-ans-heading">{result?.correct_answers}</span></div>
              <div>  <span className="quiz-result-heading">Score Percentage:</span>{" "}<span className="quiz-result-ans-heading">{result?.score_percentage}%</span></div>
            </div>
            <div style={{
              width: '100%',
              display: 'flex',
              justifyContent: 'center',
              alignItems: 'center',
              margin: '0 auto',
              maxWidth: '400px',
              height: 'auto',
            }}>
              <BasicPie
                response={result}
              />
            </div>
          </DialogContentText>
        </DialogContent>
        <DialogActions>
          <Button onClick={handleClose}>Close</Button>
        </DialogActions>
      </Dialog>
    </>
  )
}

export default Page;