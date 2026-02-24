"use client"
import Footer from "@/components/Footer";
import Loader from "@/components/Loader";
import Navbar from "@/components/Navbar";
import baseUrl from "@/Services/BaseUrl";
import axios from "axios";
import Cookies from "js-cookie";
import Link from "next/link";
import { useSearchParams } from "next/navigation";
import { useRouter } from "next/navigation";
import { Suspense, useEffect, useRef, useState } from "react";
import { toast } from "react-toastify";
import Button from '@mui/material/Button';
import Dialog from '@mui/material/Dialog';
import DialogActions from '@mui/material/DialogActions';
import DialogContent from '@mui/material/DialogContent';
import DialogContentText from '@mui/material/DialogContentText';
import DialogTitle from '@mui/material/DialogTitle';

const Page = () => {
  const router = useRouter()
  const searchParams = useSearchParams()
  const paramsId = searchParams.get('id')
  const [loading, setLoading] = useState(false);
  const [allData, setAllData] = useState([])
  const [allQuestions, setAllQuestions] = useState([])
  const [selectedQuestionIndex, setSelectedQuestionIndex] = useState(0);
  const [selectedAnswer, setSelectedAnswer] = useState();
  const [allAns, setAllAns] = useState([]);
  const [checkAnswer, setCheckAnswer] = useState(false);
  const [showAnswer, setShowAnswer] = useState(false);
  const [firstRendered, setFirstRendered] = useState(false)
  const [open, setOpen] = useState(false);
  const [showResults, setShowResults] = useState(false);
  const [quizResults, setQuizResults] = useState({ correct: 0, incorrect: 0, total: 0, percentage: 0 });

  useEffect(() => {
    const IsUserExist = Cookies.get('user-token')
    setLoading(true);
    if (!IsUserExist) {
      return router.push('/')
    } else {
      if (paramsId) getData()
    }
  }, []);

  const isFirstRender = useRef(true);

  useEffect(() => {
    if (isFirstRender.current) {
      isFirstRender.current = false;
      return;
    }

    return () => {
      cleanupFunc()
    };
  }, []);

  const cleanupFunc = () => {
    // console.log('firstRendered', firstRendered)
  }

  const handleClickOpen = () => {
    setOpen(true);
  };

  const handleClose = () => {
    setOpen(false);
  };


  const getData = async () => {
    const cookies = Cookies.get('user-token');
    try {
      axios.get(`${baseUrl}/api/quiz/${paramsId}`, {
        headers: { 'Authorization': `Bearer ${cookies}` }
      })
        .then((response) => {
          setAllData(response.data.data);
          setAllQuestions(response.data.data.questions);
          setLoading(false);
        })
    } catch (error) {
      console.error(error);
      setLoading(false);
      // } finally {
      //   setLoading(false);
      // }
    }
  }

  const checkAnswerFunc = () => {
    // if (!selectedAnswer) return toast.error("Please select an answer first");
    setCheckAnswer(!checkAnswer)
    setShowAnswer(true)
  }

  const nextAnswerFunc = () => {
    let question = allQuestions[selectedQuestionIndex]
    const cookies = Cookies.get('user-token');
    const user_id = Cookies.get('user-id');

    try {
      // axios.post(`${baseUrl}/api/quiz/submit-response`, {
      //   quiz_id: question.quiz_id, user_id: user_id,
      //   responses: [
      //     {
      //       question_id: question.id,
      //       selected_answer_id: selectedAnswer.id,
      //       status: "attempted"
      //     }],
      // }, {
      //   headers: { 'Authorization': `Bearer ${cookies}` }
      // })
      //   .then((response) => {
      //     setCheckAnswer(false)
      //     setShowAnswer(false)
      //     setSelectedAnswer()
      //     setSelectedQuestionIndex(selectedQuestionIndex + 1)
      //     window.scrollTo({ top: 0, behavior: "smooth" });
      //     completeAnswerFunc()
      //   })
      let ans = [...allAns, { question_id: question.id, selected_answer_id: selectedAnswer.id, status: "attempted" }]
      setAllAns(ans)
      setCheckAnswer(false)
      setShowAnswer(false)
      setSelectedAnswer()
      setSelectedQuestionIndex(selectedQuestionIndex + 1)
      window.scrollTo({ top: 0, behavior: "smooth" });
      completeAnswerFunc(ans)
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  }

  const quitQuizFunc = () => {
    let question = allQuestions[selectedQuestionIndex]
    const cookies = Cookies.get('user-token');
    const user_id = Cookies.get('user-id');

    // try {
    // axios.post(`${baseUrl}/api/quiz/submit-response`, {
    //   quiz_id: question.quiz_id, user_id: user_id,
    //   responses: [
    //     {
    //       question_id: question.id,
    //       selected_answer_id: 0,
    //       status: "unattempted"
    //     }],
    // }, {
    //   headers: { 'Authorization': `Bearer ${cookies}` }
    // })
    //   .then((response) => {
    //      router.back()
    //      handleClose()
    //     })
    // } catch (error) {
    //   console.error(error);
    // } finally {
    //   setLoading(false);


    try {
      axios.post(`${baseUrl}/api/quiz/submit-response`, {
        quiz_id: question.quiz_id, user_id: user_id,
        responses: allAns,
      }, {
        headers: { 'Authorization': `Bearer ${cookies}` }
      })
        .then((response) => {
          router.back()
          handleClose()
        })
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  }

  const completeAnswerFunc = (allAns) => {
    let question = allQuestions[selectedQuestionIndex]
    const cookies = Cookies.get('user-token');
    const user_id = Cookies.get('user-id');

    try {
      if (selectedQuestionIndex + 1 === allQuestions.length) {
        // Calculate quiz results
        let correct = 0;
        let incorrect = 0;
        let total = allQuestions.length;

        allAns.forEach((answer) => {
          const question = allQuestions.find(q => q.id === answer.question_id);
          if (question) {
            const correctAnswer = question.answers.find(a => a.is_correct === 1);
            if (correctAnswer && correctAnswer.id === answer.selected_answer_id) {
              correct++;
            } else {
              incorrect++;
            }
          }
        });

        const percentage = Math.round((correct / total) * 100);
        setQuizResults({ correct, incorrect, total, percentage });

        axios.post(`${baseUrl}/api/quiz/submit-response`,
          { quiz_id: question.quiz_id, user_id: user_id, responses: allAns, },
          { headers: { 'Authorization': `Bearer ${cookies}` } }
        ).then((response) => {
          axios.post(`${baseUrl}/api/quiz/change-status`,
            { status: 'completed', quiz_id: question.quiz_id, user_id: user_id }, {
            headers: { 'Authorization': `Bearer ${cookies}` }
          }).then((response) => {
            toast.success('Congratulations! Quiz completed.');
            setShowResults(true);
          })
        })
      } else {
        // axios.post(`${baseUrl}/api/quiz/change-status`, { status: 'paused', quiz_id: question.quiz_id, user_id: user_id }, {
        //   headers: { 'Authorization': `Bearer ${cookies}` }
        // }).then((response) => {
        // })
      }
    } catch (error) {
      console.error(error);
    } finally {
      setLoading(false);
    }
  }

  const [isActive, setIsActive] = useState(false);

  const saveosce = (e) => {
    console.log(e)

    const cookies = Cookies.get('user-token');
    const userid = Cookies.get('user-id');
    const formData = new FormData();
    formData.append('user_id', userid);
    formData.append('quiz_id', e.quiz_id);
    formData.append('question_id', e.id);

    axios.post(`${baseUrl}/api/quiz/change-bookmark`, formData, {
      headers: {
        'Authorization': `Bearer ${cookies}`,
      }
    }).then((response) => {
      console.log(response.data);
      toast.success(response.data.message);

      setIsActive(true);
      setTimeout(() => {
        setIsActive(false);
      }, 6000);
    })
      .catch((error) => {
        console.error('There was an error!', error);
      });
  }

  return (
    <>
      <Navbar />
      {!loading ? (
        <>
          {allQuestions?.length !== 0 && (
            <div className="inner-page">
              <div className="Quiz-wrapper">
                <div className="top-tab-list">
                  <div className="container">
                    <div className="row">
                      <div className="col-lg-4">
                        <h2>QUIZORA</h2>
                      </div>

                      <div className="col-lg-4" style={{ lineHeight: '24px' }}>
                        
                      </div>
                      <div className="col-lg-8">
                        <div className="swiper-pagination">
                          <nav aria-label="Page navigation">
                            <ul className="pagination justify-content-end mb-0">
                              <li className="page-item" onClick={() => setSelectedQuestionIndex(selectedQuestionIndex - 1)} >
                                <a className="page-link prev" href="#">
                                  «
                                </a>
                              </li>
                              {allQuestions?.map((ques, i) => (
                                <li key={ques?.id} className={`page-item ${i == selectedQuestionIndex ? 'active' : ''}`}>
                                  <a className={`page-link`} href="#" data-page={i + 1}>
                                    {i + 1}
                                  </a>
                                </li>
                              ))}
                              <li className="page-item" onClick={() => {
                                if (!selectedAnswer) return toast.error("Please select an answer first");
                                else nextAnswerFunc()
                              }}>
                                <a className="page-link next" href="#">
                                  »
                                </a>
                              </li>
                              {/* <div className="pagination justify-content-end mb-0">
                                <i style={{ fontSize: '1rem' }}  class="fa-regular fa-bookmark"></i>
                              </div>
                              <div className="pagination justify-content-end mb-0">
                                <i class="fa-solid fa-bookmark"></i>
                              </div> */}
                              <button onClick={() => handleClickOpen()} className="btn checkBtn">Go Back</button>
                            </ul>
                          </nav>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <div className="container" style={{ marginTop: '40px' }}>
                  <div
                    className="macaroni-sign-wrap p-4"
                    style={{ backgroundColor: "#fff" }}
                  >
                    <div className="row">
                      {allQuestions?.[selectedQuestionIndex]?.image_url ? (
                        <>
                          <div className="col-lg-4 sticky-top mb-5">
                            <div className="image">
                              <img
                                src={allQuestions?.[selectedQuestionIndex]?.image_url}
                                className="img-fluid w-100 mb-4"
                                alt="Macaroni Sign"
                              />
                              {/* <h6 className="text-center">Image Title</h6> */}
                            </div>
                          </div>
                          <div className="col-lg-8">
                            <div className="macaroni-sign-inner">
                              <div className="swiper-container">
                                <div className="swiper-wrapper">
                                  <div className="swiper-slide">
                                    <div className="row">
                                      <div className="col-lg-11">
                                        <h5 className="text-dark mb-3">
                                          {allQuestions?.[selectedQuestionIndex]?.question_text}
                                        </h5>
                                      </div>
                                      <div className="col-lg-1" style={{ color: '#000' }}>
                                        <div>
                                          <div
                                            className={`icon ${isActive ? "bookmark-active" : ""}`}
                                            onClick={() => saveosce(allQuestions?.[selectedQuestionIndex])}>
                                            <i className="fa-solid fa-bookmark" />
                                          </div>
                                        </div>
                                      </div>
                                    </div>
                                    <span className="questions">Select the correct option</span>
                                    {allQuestions?.[selectedQuestionIndex]?.answers?.map((ans, i) => (
                                      <div
                                        key={ans?.id}
                                        onClick={() => {
                                          if (!checkAnswer && !selectedAnswer) {
                                            setSelectedAnswer(ans)
                                            checkAnswerFunc()
                                          }
                                        }}
                                        className={`option mb-3 
                                      ${!checkAnswer && selectedAnswer?.id === ans.id ? 'selected' : ''} 
                                      ${checkAnswer && ans.is_correct ? 'correct' : ''}
                                      ${checkAnswer && selectedAnswer?.id === ans.id && ans.is_correct === 0 ? 'incorrect' : ''}
                                      `}
                                      // className={`option mb-3 
                                      // ${selectedAnswer && ans.is_correct ? 'correct' : ''}
                                      // ${selectedAnswer?.id === ans.id && ans.is_correct === 0 ? 'incorrect' : ''}
                                      // `}
                                      >
                                        <span>{ans?.option_text}</span>
                                        <span>{checkAnswer && ans.is_correct ? <i class="fa-regular fa-circle-check"></i>
                                          : checkAnswer && selectedAnswer?.id === ans.id && ans.is_correct === 0 ? <i class="fa-regular fa-circle-xmark"></i> : null}
                                        </span>
                                      </div>
                                    ))}
                                    {/*    {!checkAnswer && (
                                      <>
                                        <div className="check-button d-flex justify-content-end my-4">
                                          <button onClick={() => checkAnswerFunc()} className="btn checkBtn">Show Answer</button>
                                        </div>
                                      </>
                                    )}  */}

                                    {/* {checkAnswer && !showAnswer && (
                                  <>
                                    <div className="check-button d-flex justify-content-end my-4">
                                      <button onClick={() => setShowAnswer(true)} className="btn checkBtn">Check Answer</button>
                                    </div>
                                  </>
                                )} */}

                                    <h4 className='quiz-explainaton-heading'>Explanation:</h4>

                                    {showAnswer && (
                                      <>
                                        <div className="result" id="result" />
                                        {allQuestions?.[selectedQuestionIndex]?.explanation && (
                                          <div className="explanation mb-4" id="explanation-section">
                                            <div className="row">
                                              <div className="col-lg-12">
                                                <div dangerouslySetInnerHTML={{ __html: allQuestions?.[selectedQuestionIndex]?.explanation }} />
                                              </div>
                                            </div>
                                          </div>
                                        )}
                                        <div className="check-button d-flex justify-content-end my-4">
                                          <button onClick={() => nextAnswerFunc()} className="btn checkBtn">
                                            {selectedQuestionIndex + 1 === allQuestions.length ? 'Complete' : 'Next'}
                                          </button>
                                        </div>
                                      </>
                                    )}

                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </>
                      ) : (
                        <>
                          <div className="col-lg-12">
                            <div className="macaroni-sign-inner">
                              <div className="swiper-container">
                                <div className="swiper-wrapper">
                                  <div className="swiper-slide">
                                    <h5 className="text-dark mb-3">
                                      {allQuestions?.[selectedQuestionIndex]?.question_text}
                                    </h5>
                                    <span className="questions">Select the correct option</span>
                                    {allQuestions?.[selectedQuestionIndex]?.answers?.map((ans, i) => (
                                      <div
                                        key={ans?.id}
                                        onClick={() => {
                                          // if (!checkAnswer) setSelectedAnswer(ans)
                                          setSelectedAnswer(ans)
                                          checkAnswerFunc()
                                        }}
                                        className={`option mb-3 
                                      ${!checkAnswer && selectedAnswer?.id === ans.id ? 'selected' : ''} 
                                      ${checkAnswer && ans.is_correct ? 'correct' : ''}
                                      ${checkAnswer && selectedAnswer?.id === ans.id && ans.is_correct === 0 ? 'incorrect' : ''}
                                      `}
                                      // className={`option mb-3 
                                      // ${selectedAnswer && ans.is_correct ? 'correct' : ''}
                                      // ${selectedAnswer?.id === ans.id && ans.is_correct === 0 ? 'incorrect' : ''}
                                      // `}
                                      >
                                        <span>{ans?.option_text}</span>
                                        <span>{checkAnswer && ans.is_correct ? <i class="fa-regular fa-circle-check"></i>
                                          : checkAnswer && selectedAnswer?.id === ans.id && ans.is_correct === 0 ? <i class="fa-regular fa-circle-xmark"></i> : null}
                                        </span>
                                      </div>
                                    ))}
                                    {/* {!checkAnswer && (
                                      <>
                                        <div className="check-button d-flex justify-content-end my-4">
                                          <button onClick={() => checkAnswerFunc()} className="btn checkBtn">Show Answer</button>
                                        </div>
                                      </>
                                    )} */}

                                    {/* {checkAnswer && !showAnswer && (
                                  <>
                                    <div className="check-button d-flex justify-content-end my-4">
                                      <button onClick={() => setShowAnswer(true)} className="btn checkBtn">Check Answer</button>
                                    </div>
                                  </>
                                )} */}

                                    {showAnswer && (
                                      <>
                                        <h4 className='quiz-explainaton-heading'>Explanation:</h4>
                                        <div className="result" id="result" />
                                        {allQuestions?.[selectedQuestionIndex]?.explanation && (
                                          <div className="explanation mb-4" id="explanation-section">
                                            <div className="row">
                                              <div className="col-lg-12">
                                                <div dangerouslySetInnerHTML={{ __html: allQuestions?.[selectedQuestionIndex]?.explanation }} />
                                              </div>
                                            </div>
                                          </div>
                                        )}
                                        <div className="check-button d-flex justify-content-end my-4">
                                          <button onClick={() => nextAnswerFunc()} className="btn checkBtn">
                                            {selectedQuestionIndex + 1 === allQuestions.length ? 'Complete' : 'Next'}
                                          </button>
                                        </div>
                                      </>
                                    )}

                                  </div>
                                </div>
                              </div>
                            </div>
                          </div>
                        </>
                      )}
                    </div>
                  </div>
                </div>
              </div>
            </div>
          )}
          {/* Quit Confirmation Dialog */}
          <Dialog
            open={open}
            onClose={handleClose}
            aria-labelledby="alert-dialog-title"
            aria-describedby="alert-dialog-description"
          >
            <DialogContent>
              <DialogContentText id="alert-dialog-description">
                Do you want to quit and go back?
              </DialogContentText>
            </DialogContent>
            <DialogActions>
              <Button onClick={handleClose}>No</Button>
              <Button onClick={() => {
                quitQuizFunc()
              }} autoFocus style={{ background: 'red', color: '#fff' }}>
                Yes
              </Button>
            </DialogActions>
          </Dialog>

          {/* Quiz Results Dialog */}
          <Dialog
            open={showResults}
            onClose={() => setShowResults(false)}
            aria-labelledby="results-dialog-title"
            maxWidth="sm"
            fullWidth
            PaperProps={{
              style: {
                borderRadius: '16px',
                padding: '20px'
              }
            }}
          >
            <DialogTitle id="results-dialog-title" style={{ textAlign: 'center', fontSize: '24px', fontWeight: '700', color: '#126E97' }}>
              🎉 Quiz Completed!
            </DialogTitle>
            <DialogContent>
              <div style={{ textAlign: 'center', padding: '20px 0' }}>
                <div style={{ 
                  fontSize: '72px', 
                  fontWeight: '700', 
                  color: quizResults.percentage >= 70 ? '#22c55e' : quizResults.percentage >= 50 ? '#f59e0b' : '#ef4444',
                  marginBottom: '10px'
                }}>
                  {quizResults.percentage}%
                </div>
                <div style={{ fontSize: '18px', color: 'rgba(0,0,0,0.6)', marginBottom: '30px' }}>
                  Your Score
                </div>
                <div style={{
                  background: 'linear-gradient(135deg, rgba(18, 110, 151, 0.05) 0%, rgba(18, 110, 151, 0.02) 100%)',
                  borderRadius: '12px',
                  padding: '20px',
                  textAlign: 'left'
                }}>
                  <div style={{ display: 'flex', justifyContent: 'space-between', padding: '10px 0', borderBottom: '1px solid rgba(0,0,0,0.1)' }}>
                    <span style={{ fontSize: '16px', fontWeight: '500' }}>Total Questions:</span>
                    <span style={{ fontSize: '16px', fontWeight: '600', color: '#126E97' }}>{quizResults.total}</span>
                  </div>
                  <div style={{ display: 'flex', justifyContent: 'space-between', padding: '10px 0', borderBottom: '1px solid rgba(0,0,0,0.1)' }}>
                    <span style={{ fontSize: '16px', fontWeight: '500' }}>Correct Answers:</span>
                    <span style={{ fontSize: '16px', fontWeight: '600', color: '#22c55e' }}>{quizResults.correct}</span>
                  </div>
                  <div style={{ display: 'flex', justifyContent: 'space-between', padding: '10px 0' }}>
                    <span style={{ fontSize: '16px', fontWeight: '500' }}>Incorrect Answers:</span>
                    <span style={{ fontSize: '16px', fontWeight: '600', color: '#ef4444' }}>{quizResults.incorrect}</span>
                  </div>
                </div>
                {quizResults.percentage >= 70 && (
                  <div style={{ marginTop: '20px', fontSize: '16px', color: '#15803d', fontWeight: '500' }}>
                    ✨ Excellent Performance!
                  </div>
                )}
                {quizResults.percentage >= 50 && quizResults.percentage < 70 && (
                  <div style={{ marginTop: '20px', fontSize: '16px', color: '#d97706', fontWeight: '500' }}>
                    👍 Good Job! Keep Practicing!
                  </div>
                )}
                {quizResults.percentage < 50 && (
                  <div style={{ marginTop: '20px', fontSize: '16px', color: '#dc2626', fontWeight: '500' }}>
                    💪 Keep Learning! You'll Do Better Next Time!
                  </div>
                )}
              </div>
            </DialogContent>
            <DialogActions style={{ justifyContent: 'center', padding: '20px' }}>
              <Button 
                onClick={() => router.push('/quizora')} 
                variant="contained"
                style={{ 
                  background: 'linear-gradient(135deg, #126E97 0%, #0e5a7a 100%)',
                  color: '#fff',
                  padding: '12px 40px',
                  fontSize: '16px',
                  fontWeight: '600',
                  borderRadius: '8px',
                  textTransform: 'none',
                  boxShadow: '0 4px 12px rgba(18, 110, 151, 0.25)'
                }}
              >
                Go Back to Quizora
              </Button>
            </DialogActions>
          </Dialog>
        </>
      ) : (
        <Loader />
      )}
      <Footer />
    </>
  )
}

// export default Page;


export default function SusPage() {
  return (
    <Suspense>
      <Page />
    </Suspense>
  )
}