import * as d3 from 'd3';
import QueryString from './QueryString';

type Datum = {
  timestamp: string;
  values: {
    [key: string]: number;
  };
};
type DataSet = Datum[];

// roygbiv warm from https://kgolid.github.io/chromotome-site/
const colors = [
  '#dc383a',
  '#fc9a1a',
  '#6c843e',
  '#705f84',
  '#aa3a33',
  '#687d99',
  '#9c4257',
  '#347634',
];
let serieses: string[];
let data: DataSet;

function getQuery() {
  let meta = [];

  if (Array.isArray(QueryString.meta)) {
    meta = QueryString.meta;
  } else {
    meta.push(QueryString.meta);
  }

  return meta.map((val) => `meta[]=${val}`).join('&');
}

function fetchData() {
  const query = getQuery();
  const url = `/ajax/diary?${query}`;
  const request = new Request(url, {
    method: 'GET',
    headers: {
      Accept: 'application/json',
      'Content-Type': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    },
  });

  return fetch(request)
    .then((response) => response.json())
    .then((response) => {
      serieses = response.series;
      data = response.data.map((datum: { date: string; [key: string]: string }) => ({
        ...datum,
        date: new Date(datum.date),
      }));
    });
}

function makeExtent() {
  const yExtent = [null, null] as [number?, number?];

  data.forEach((datum: Datum) => {
    for (let i = 0; i < serieses.length; i++) {
      if (
        yExtent[0] === null ||
        (datum.values[serieses[i]] < yExtent[0] && datum.values[serieses[i]] !== null)
      ) {
        yExtent[0] = datum.values[serieses[i]];
      }

      if (
        yExtent[1] === null ||
        (datum.values[serieses[i]] > yExtent[1] && datum.values[serieses[i]] !== null)
      ) {
        yExtent[1] = datum.values[serieses[i]];
      }
    }
  });

  return yExtent;
}

function setupGraph() {
  const xValue = (d: Datum) => new Date(d.timestamp);
  const xLabel = 'Time';
  const margin = { left: 40, right: 40, top: 40, bottom: 40 };
  const svg = d3.select('#graph');
  const width = parseInt(svg.attr('width'), 10);
  const height = parseInt(svg.attr('height'), 10);
  const innerWidth = width - margin.left - margin.right;
  const innerHeight = height - margin.top - margin.bottom;
  const g = svg.append('g').attr('transform', `translate(${margin.left},${margin.top})`);
  const xAxisG = g.append('g').attr('transform', `translate(0, ${innerHeight})`);
  const xScale = d3.scaleTime();
  const xAxis = d3.axisBottom(xScale).scale(xScale).tickPadding(15).tickSize(-innerHeight);
  const yValues: { [key: string]: any } = {};
  const gs: { [key: string]: any } = {};
  const yAxisG = g.append('g');
  const yScale = d3.scaleLinear();
  const yExtent = makeExtent();
  const yAxis = d3.axisLeft(yScale).scale(yScale).ticks(5).tickPadding(15).tickSize(-innerWidth);

  xAxisG
    .append('text')
    .attr('class', 'axis-label')
    .attr('x', innerWidth / 2)
    .attr('y', 100)
    .text(xLabel);

  xScale.domain(d3.extent(data, xValue)).range([0, innerWidth]).nice();

  yScale.domain(yExtent).range([innerHeight, 0]).nice();

  yAxisG.call(yAxis);

  serieses.forEach((series, index) => {
    gs[series] = svg.append('g').attr('transform', `translate(${margin.left},${margin.top})`);

    yValues[series] = (d: Datum) => d.values[series];

    gs[series]
      .selectAll('circle')
      .data(data)
      .enter()
      .append('circle')
      .filter((d: Datum) => d.values[series] !== null)
      .attr('cx', (d: Datum) => xScale(xValue(d)))
      .attr('cy', (d: Datum) => yScale(yValues[series](d)))
      .attr('fill-opacity', 0.8)
      .attr('fill', colors[index % colors.length])
      .attr('r', 8);
  });

  xAxisG.call(xAxis);
}

function setupLegend() {
  const itemHeight = 50;
  var svg = d3.select('#legend');
  var color = d3.scaleOrdinal().domain(serieses).range(colors);

  svg.attr('height', serieses.length * itemHeight + 100 + 'px');

  svg
    .selectAll('mydots')
    .data(serieses)
    .enter()
    .append('circle')
    .attr('cx', 40)
    .attr('cy', function (d, i) {
      return 20 + i * 25;
    })
    .attr('r', 8)
    .attr('fill', (d) => <string>color(d));

  svg
    .selectAll('mylabels')
    .data(serieses)
    .enter()
    .append('text')
    .attr('x', 60)
    .attr('y', (d, i) => 25 + i * 25)
    .attr('fill', (d): string => <string>color(d))
    .text((d) => d)
    .attr('text-anchor', 'left')
    .style('alignment-baseline', 'middle');
}

async function go() {
  await fetchData();
  setupGraph();
  setupLegend();
}

go();
