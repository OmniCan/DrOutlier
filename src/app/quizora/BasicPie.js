import * as React from 'react';
import { PieChart, pieArcLabelClasses } from '@mui/x-charts/PieChart';
import { colors } from '@mui/material';

export default function PieArcLabel({ response }) {
  let { total_questions, correct_answers } = response
  let wronganswer = total_questions - correct_answers

  const desktopOS = [
    { id: 0, value: correct_answers, label: 'Correct Answer' },
    { id: 1, value: wronganswer, label: 'Incorrect Answer' , color :'red'},
  ];

  const valueFormatter = (value) => `${value}`;

  return (
    <PieChart
      series={[
        {
          arcLabel: (item) => `${item.value}`,
          arcLabelMinAngle: 35,
          arcLabelRadius: '60%',
          data: desktopOS,
          valueFormatter,
        },
      ]}
      sx={{
        [`& .${pieArcLabelClasses.root}`]: {
          fontWeight: 'bold',
        },
      }}
      {...size}
    />
  );
}

const size = {
  width: 560,
  height: 330,
};
