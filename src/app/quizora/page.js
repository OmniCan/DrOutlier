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
  const [status, setStatus] = useState('')
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
        // getDataSaved()
        setTimeout(() => {
          sessionStorage.removeItem('is_saved')
        }, 1000);
      } else {
        // getData()
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
            <div className="top-tab-list">
              <div className="container">
                <div className="row">
                  <div className="col-lg-12">
                    <ul className="nav nav-tabs" id="myTab" role="tablist">
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === '' ? 'active' : ''}`}
                          // id="all-tab"
                          data-bs-toggle="tab"
                          // href="#all"
                          // role="tab"
                          aria-controls="all"
                          aria-selected="true"
                          onClick={() => setStatus('')}
                        >
                          All
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'paused' ? 'active' : ''}`}
                          // id="paused-tab"
                          data-bs-toggle="tab"
                          // href="#paused"
                          // role="tab"
                          aria-controls="paused"
                          aria-selected="false"
                          onClick={() => setStatus('paused')}
                        >
                          Paused
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'completed' ? 'active' : ''}`}
                          // id="completed-tab"
                          data-bs-toggle="tab"
                          // href="#completed"
                          // role="tab"
                          aria-controls="completed"
                          aria-selected="false"
                          onClick={() => setStatus('completed')}
                        >
                          Completed
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'unattempted' ? 'active' : ''}`}
                          // id="unattempted-tab"
                          data-bs-toggle="tab"
                          // href="#unattempted"
                          // role="tab"
                          aria-controls="unattempted"
                          aria-selected="false"
                          onClick={() => setStatus('unattempted')}
                        >
                          Unattempted
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'free' ? 'active' : ''}`}
                          // id="free-tab"
                          data-bs-toggle="tab"
                          // href="#free"
                          // role="tab"
                          aria-controls="free"
                          aria-selected="false"
                          onClick={() => setStatus('free')}
                        >
                          Free
                        </a>
                      </li>
                      <li className="nav-item" role="presentation">
                        <a
                          className={`nav-link ${status === 'saved' ? 'active' : ''}`}
                          data-bs-toggle="tab"
                          aria-controls="saved"
                          aria-selected="false"
                          onClick={() => setStatus('saved')}
                        >
                          Saved
                        </a>
                      </li>
                    </ul>
                  </div>
                </div>
              </div>
            </div>
            <div className="top-tab-content-wrap">
              <div className="container">
                <div className="row">
                  <div className="col-lg-12">
                    <section className="top-tab-content bg-white  p-4">
                      <div className="tab-content" id="myTabContent">
                        <div
                          className="tab-pane fade show active"
                          id="all"
                          role="tabpanel"
                          aria-labelledby="all-tab"
                        >
                          {allData?.length === 0 ? (
                            <>
                              No Data
                            </>
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
                                            <div key={category?.id} className="quiz-wrap mb-5">
                                              <p className="text">
                                                {category?.name}
                                              </p>
                                              {category?.quizzes?.map((quiz, i) => (
                                                <div key={quiz?.id} className="quiz-box-wrap">
                                                  <div className="quiz-box mb-3">
                                                    <span className="number">{i + 1}</span>
                                                    <div className="row align-items-center">
                                                      <div className="col-lg-2 col-md-3">
                                                        <div className="image">
                                                          <img
                                                            src={quiz?.image_url ? quiz?.image_url : "/images/quiz.webp"}
                                                            className="img-fluid"
                                                            alt=""
                                                          />
                                                        </div>
                                                      </div>
                                                      <div className="col-lg-8 col-md-6">
                                                        <div className="content">
                                                          {quiz?.quiz_status === 0 ? (
                                                            <button className="btn BtnUnattempted">
                                                              Unattempted
                                                            </button>
                                                          ) : quiz?.quiz_status === 1 ? (
                                                            <button className="btn BtnCompleted">
                                                              Completed <i class="fa-regular fa-circle-check"></i>
                                                            </button>
                                                          ) : quiz?.quiz_status === 2 ? (
                                                            <button className="btn BtnPaused">Paused</button>
                                                          ) : (
                                                            <button className="btn BtnPaused">{status}</button>
                                                          )}
                                                          <h5>
                                                            {quiz?.name}
                                                          </h5>
                                                          <span className="questions">{quiz?.quiz_status === 2 ? `${quiz?.total_questions - quiz?.unattemptedQuestionsCount}/` : null}{quiz?.total_questions}{" "}{quiz?.total_questions > 1 ? 'Questions' : 'Question'}</span>
                                                        </div>
                                                      </div>
                                                      <div className="col-lg-2 col-md-3">
                                                        <div className="button-wrap">
                                                          {quiz?.quiz_status === 1 ? (
                                                            <span onClick={() => getResult(quiz.id, quiz)} className="btn btnQuiz result-btn">
                                                              See Result
                                                            </span>
                                                          ) : null}
                                                        </div>
                                                        <div className="button-wrap">
                                                          <Link href={`/quiz?id=${quiz?.id}`} className="btn btnQuiz">
                                                            {quiz?.quiz_status === 0 ? (
                                                              <span>Start Quiz</span>
                                                            ) : quiz?.quiz_status === 1 ? (
                                                              <span>Start Quiz Again</span>
                                                            ) : quiz?.quiz_status === 2 ? (
                                                              <span>Continue</span>
                                                            ) : (
                                                              <span>Start Quiz</span>
                                                            )}
                                                          </Link>
                                                        </div>
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
                                          <div key={quiz?.id} className="quiz-wrap mb-5">
                                            <div key={quiz?.id} className="quiz-box-wrap">
                                              <div className="quiz-box mb-3">
                                                <span className="number">{i + 1}</span>
                                                <div className="row align-items-center">
                                                  <div className="col-lg-2 col-md-3">
                                                    <div className="image">
                                                      <img
                                                        onClick={() => console.log(quiz)}
                                                        src={quiz?.image_url ? quiz?.image_url : "/images/quiz.webp"}
                                                        className="img-fluid"
                                                        alt=""
                                                      />
                                                    </div>
                                                  </div>
                                                  <div className="col-lg-8 col-md-6">
                                                    <div className="content">
                                                      {quiz?.quiz_status === 0 ? (
                                                        <button className="btn BtnUnattempted">
                                                          Unattempted
                                                        </button>
                                                      ) : quiz?.quiz_status === 1 ? (
                                                        <button className="btn BtnCompleted">
                                                          Completed <i class="fa-regular fa-circle-check"></i>
                                                        </button>
                                                      ) : quiz?.quiz_status === 2 ? (
                                                        <button className="btn BtnPaused">Paused</button>
                                                      ) : (
                                                        <button className="btn BtnPaused">{status}</button>
                                                      )}
                                                      <h5>
                                                        {quiz?.quiz?.name}
                                                      </h5>
                                                      <span className="questions">{quiz?.quiz_status === 2 ? `${quiz?.total_questions - quiz?.unattemptedQuestionsCount}/` : null}{quiz?.total_questions}{" "}{quiz?.total_questions > 1 ? 'Questions' : 'Question'}</span>
                                                    </div>
                                                  </div>
                                                  <div className="col-lg-2 col-md-3">
                                                    <div className="button-wrap">
                                                      {quiz?.quiz_status === 1 ? (
                                                        <span onClick={() => getResult(quiz.id, quiz)} className="btn btnQuiz result-btn">
                                                          See Result
                                                        </span>
                                                      ) : null}
                                                    </div>
                                                    <div className="button-wrap">
                                                      <Link href={`/quiz?id=${quiz?.quiz?.id}`} className="btn btnQuiz">
                                                        {quiz?.quiz_status === 0 ? (
                                                          <span>Start Quiz</span>
                                                        ) : quiz?.quiz_status === 1 ? (
                                                          <span>Start Quiz Again</span>
                                                        ) : quiz?.quiz_status === 2 ? (
                                                          <span>Continue</span>
                                                        ) : (
                                                          <span>Start Quiz</span>
                                                        )}
                                                      </Link>
                                                    </div>
                                                  </div>
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